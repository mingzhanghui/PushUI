<?php
/**
 * Created by PhpStorm.
 * User: mzh
 * Date: 2017-01-22
 * Time: 14:27
 */
namespace Content\Controller;
use Content\Model\AppendixModel;
use Content\Model\EditstatusModel;
use Content\Model\MediaModel;
use Content\Model\MediatypeModel;
use Content\Model\PathModel;
use Content\Model\SeriesepisodeModel;
use Content\Model\SpecialepisodeModel;
use Content\Model\TvshowepisodeModel;
use Home\Model\ConfigureModel;
use Think\Controller;

use Common\Common\Config;

class SearchController extends Controller {

    public function __construct() {
        parent::__construct();
        // ?login
        if(!isset($_SESSION)){
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['content']) {
                redirect(U('Content/Login/index').'?refer='.U());
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['content'] = 1;
        }
    }

    // 内容编辑 - 内容查询
    public function index() {
        $flag = 0;   // 0: 全部分类, 1: 总集分类, 2: 分集分类
        $where = "mediatypeid in (5,6,8) or (editstatus>1 and mediatypeid in (1,4,7))";

        $media = new MediaModel();

        // 按条件查询
        if (isset($_GET['mediatypeid'])) {
            $type = intval( I("get.mediatypeid") );
            setcookie("mediatypeid", $type, time() + 60 * 5); // 5min

            // 电视剧总集 or: 电视节目总集
            if ($type == 5 || $type == 6) {
                $where = array('mediatypeid' => $type);
                $flag = 1;
            } else if (in_array($type, array(1,4,7))) {
                unset($where);
                $where = array(
                    'editstatus'  => array('GT', 1),
                    'mediatypeid' => $type
                );
                $flag = 2;
            }
        }
        $title = I("get.title");
        setcookie("title", $title, time() + 60 * 5);
        if (isset($title) && trim($title) !== "") {
            if ($flag) {
                $where['title'] = array('LIKE', '%' . $title . '%');
            } else {
                $where .= " and title like '%".$title."%'";
            }
        }

        // 查询满足要求的总记录数 for pagination
        if ($flag == 1) {
            $count = $media->where($where)->count();
        } else {
            $count = $media->join('MBIS_Server_EditStatus ON MBIS_Server_EditStatus.OID = MBIS_Server_Media.OID', 'left')
                ->where($where)->count();
        }
        $Page = new \Think\Page($count, 12); // 实例化分页类 传入总记录数和每页显示的记录数 12

        $Page->setConfig('header', '<span class="rows">共 %TOTAL_ROW% 条记录</span>');
        $Page->rollPage = 3; // 页面显示5个跳转a链接
        $show = $Page->show(); //  分页显示输出
        $this->assign('page', $show);

        // 查出所有的分集 ( 进行分页数据查询 注意 limit 方法的参数要使用 Page 类的属性 )
        if ($flag == 1) {
            $fields = array('oid', 'title', 'mediatypeid');
            $all = $media
                ->where($where)->field($fields)->order("id desc")
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->select();
        } else {
            $fields = array('MBIS_Server_Media.oid as oid', 'editstatus', 'slicestatus', 'title', 'mediatypeid');

            $all = $media->join('MBIS_Server_EditStatus ON MBIS_Server_EditStatus.OID = MBIS_Server_Media.OID', 'LEFT')
                ->where($where)->field($fields)->order("MBIS_Server_EditStatus.ID desc")
                ->limit($Page->firstRow . ',' . $Page->listRows)
                ->select();
        }
        // header("Content-type: text/html; charset=utf-8"); echo '<pre>'; dump($all);

        $Mediatype = new \Content\Model\MediatypeModel();
        $mediatypemap = $Mediatype->mapMediaType(); // id => mediatype

        // 取得ID对应的name (mediatypeid, slicestatus, editstatus)
        for ($i = 0; $i < count($all); $i++) {
            // editstatus: 0 未编辑 1 草稿 2 提交允许编辑 3 审核允许编辑
            if ($flag == 1) {
                $all[$i]['editstatus'] = 1;   // 审核状态待确定
                $all[$i]['slicestatus'] = 1;
            }
            $all[$i]['mediatype'] = $mediatypemap[$all[$i]['mediatypeid']];
            if (is_null($all[$i]['slicestatus'])) {
                $all[$i]['slicestatus'] = 1;  // 总集查不到备播状态 都设置为已备播
            }
        }

        $this->assign("medialist", $all);
        $this->assign('firstRow', $Page->firstRow);
        // $this->assign('empty','<tr class="empty"><td colspan="9">没有数据</td></tr>');

        // dropdown for diaglogs
        $this->_assignDropdown();

        // 根据configure表配置选择是否需要审核
        $config = \Common\Common\Config::getInstance();
        if ($config->hasReviewed()) {
            $this->display();
        } else {
            $this->display('Search/noreview');
        }
    }

    public function getMediaTypeByOID() {
        if (!isset($_GET['oid'])) {
            throw_exception(__METHOD__ . " get oid failed");
        }
        $model = new MediaModel();
        $mediatypeid = $model->getMediaTypeByOID(I('get.oid'));
        $res = array("mediatypeid" =>  $mediatypeid);
        $this->ajaxReturn($res);
    }

    // 响应右边显示
    public function mediaPreview() {
        if (!isset($_GET['oid'])) {
            throw_exception(__METHOD__ . ": expect oid");
        }
        $oid = trim(I("get.oid"));
        if ($oid == '') {
            $this->ajaxReturn("<pre>get oid error</pre>");
        }
        $mediatypeid = I("get.mediatypeid");
        $Media = new MediaModel();
        if (!isset($mediatypeid) || intval($mediatypeid) === 0) {
            $mediatypeid = $Media->getMediaTypeByOID($oid);
        }
        $info = $Media->mediaPreview($oid, $mediatypeid);
        foreach ($info as $key => $value) {
            if (is_string($value)) {
                $info[$key] = htmlspecialchars($value); // escape <script></script>;
            }
        }
        // dump($info);

        $html = "";
        $mt = new MediatypeModel();
        $mediatypemap = $mt->mapMediaType();
        $mediatype = $mediatypemap[$mediatypeid];
        unset($mt);

        switch ($mediatypeid) {
            case 1: // 电影
            case 5: // 电视剧总集
            case 7: // 戏曲
                $html =
                    "<div><label>片 名:</label><span>" . $info['title'] . "</span></div>" .
                    "<div><label>导 演:</label><span>" . $info['director'] . "</span></div>" .
                    "<div><label>类 型:</label><span>" . $mediatype . "</span></div>" .
                    "<div><label>主要演员:</label><span>" . $info['actor'] . "</span></div>" .
                    "<div><label>缩略图:</label><div class=\"thumb\"><img src=\"" . $info['url'] . "\"></div></div>" .
                    "<div><label>简　介:</label><div>" . $info['introduction'] . "</div></div>";
                break;
            // 电视节目总集
            case 6:
                $html =
                    "<div><label>节目名:</label><span>" . $info['title'] . "</span></div>" .
                    "<div><label>播出电视台:</label><span>" . $info['sourcefrom'] . "</span></div>" .
                    "<div><label>类 型:</label><span>" . $mediatype . "</span></div>" .
                    "<div><label>主持人:</label><span>" . $info['host'] . "</span></div>" .
                    "<div><label>缩略图:</label><div class=\"thumb\"><img src=\"" . $info['url'] . "\"></div></div>" .
                    "<div><label>简　介:</label><div>" . $info['introduction'] . "</div></div>";
                break;
            // 热点视频
            case 4:
                $html =
                    "<div><label>片 名:</label><span>" . $info['title'] . "</span></div>" .
                    "<div><label>来 源:</label><span>" . $info['resource'] . "</span></div>" .
                    "<div><label>类 型:</label><span>" . $mediatype . "</span></div>" .
                    "<div><label>播发日期:</label><span>" . $info['bftime'] . "</span></div>" .
                    "<div><label>缩略图:</label><div class=\"thumb\"><img src=\"" . $info['url'] . "\"></div></div>" .
                    "<div><label>简　介:</label><div>" . $info['introduction'] . "</div></div>";
                break;
            // 专题节目总集
            case 8:
                $html = "<div><label>片 名:</label><span>" . $info['title'] . "</span></div>";
                // 自定义属性
                $n = count($info['attrs']);
                for ($i = 0; $i < $n; $i++) {
                    $name = htmlspecialchars($info['attrs'][$i]['attrname']);
                    $value = htmlspecialchars($info['attrs'][$i]['attrval']);
                    $html .= "<div><label>" . $name . ":</label><span>" . $value . "</span></div>";
                }
                $html .= "<div><label>类 型:</label><span>" . $mediatype . "</span></div>" .
                    "<div><label>缩略图:</label><div class=\"thumb\"><img src=\"" . $info['url'] . "\"></div></div>" .
                    "<div><label>简　介:</label><div>" . $info['introduction'] . "</div></div>";
            default:
                //...
        }
        // echo "<br />" . $html;
        $this->ajaxReturn($html, 'EVAL');
    }

    // 查看详情/编辑 打开对话框加载数据
    public function mediaInfo() {
        if (!isset($_GET['oid'])) {
            throw_exception(__METHOD__ . ": expect oid");
        }
        $oid = I("get.oid");
        $mediatypeid = I("get.mediatypeid");

        $Media = new \Content\Model\MediaModel();
        if (!isset($mediatypeid) || intval($mediatypeid) === 0) {
            $mediatypeid = $Media->getMediaTypeByOID($oid);
        }

        if ($mediatypeid == 8) {
            // 专题节目总集
            $es = new EditstatusModel();
            $model = new \Content\Model\SpecialModel();
            $info = $model->getSpecialSeriesInfo($oid);
            $n = count($info['episodes']);

            for ($i = 0; $i < $n; $i++) {
                $epoid = $info['episodes'][$i]['episodeoid'];
                $info['episodes'][$i]['editstatus'] = $es->getEditStatus($epoid, 'editstatus');
            }
        } else if ($mediatypeid == 9) {
            // 专题节目分集
            $se = new SpecialepisodeModel();
            $appendix = new AppendixModel();
            $es = new EditstatusModel();

            $info = array(
                'info' => $se->getSpecial($oid),
                'pic' => $appendix->listAppendixByOID($oid),
                'cut' => $es->getEditStatus($oid, 'slicestatus'),
            );
        } else {
            // 专题节目 之外
            $info = $Media->mediaInfo($oid, $mediatypeid);
        }
        // 取得二维码
        $C = new ConfigureModel();
        $qrcodeDir = $C->getconfpathbyname('qrcode_path');
        $qrcodeDir = rtrim($qrcodeDir, '\\/');
        // 二维码相对路径
        $rel = '/'.$oid.'/qrcode.png';
        $path = $qrcodeDir .$rel;
        if (!file_exists($path)) {
            $dir = dirname($path);
            is_dir($dir) || mkdir($dir, 0777, true);

            // 在内容编辑过程中没有生成qrcode
            $typeid = intval($mediatypeid);

            switch ($typeid) {
                case 1:
                    $type = 'movie';
                    break;
                case 2:
                case 5:
                    $type = 'tv';
                    break;
                case 3:
                case 6:
                    $type = 'show';
                    break;
                default:
                    $type= 'show';
            }
            $uri = '/content/'.$type.'/info?objid='.$oid;
            $url = C('QR_URL') . $uri;
            include './ThinkPHP/Extend/Vendor/phpqrcode/phpqrcode.php';
            $errorCorrectionLevel = 3; // 容错级别
            $matrixPointSize = 1;      // 生成图片大小
            // 生成二维码图片
            $object = new \QRcode();

            $object->png($url, $path, $errorCorrectionLevel, $matrixPointSize, 2);
        }
        $dir = substr( $qrcodeDir, strlen($_SERVER['DOCUMENT_ROOT']) );
        $info['qrcode'] = $dir .$rel;

        $this->ajaxReturn($info);
    }

    public function listAppendixByOID() {
        if (!isset($_GET['oid'])) {
            \throw_exception(__METHOD__ . 'get oid failed!');
        }
        $appendix = new \Content\Model\AppendixModel();
        $oid = I('get.oid');
        $res = $appendix->listAppendixByOID($oid);
        $this->ajaxReturn($res);
    }

    // 审核
    public function review() {
        if (!isset($_GET['oid'])) {
            \throw_exception(__METHOD__ . 'get oid failed!');
        }
        $oid = I('get.oid');
        $es = new EditstatusModel();
        $res = $es->review($oid);
        $this->ajaxReturn($res);
    }

    // 审核不通过, 返回到编辑页面
    public function reviewReject() {
        if (!isset($_GET['oid'])) {
            \throw_exception(__METHOD__ . 'get oid failed!');
        }
        $oid = I('get.oid');
        $data = array("editstatus" => 1); // 退回到编辑状态
        M("editstatus")->where(array('oid' => $oid))->data($data)->save();
        setcookie("oid", $oid, time() + 5 * 60);

        $mediatype = array(
            'index' => '1',
            'tvplay' => '2',
            'tvprogram' => '3',
            'video' => '4',
            'opera' => '7',
            'special' => '9',
        );
        $typeArray = array_flip($mediatype);

        $mediatypeid = I('get.mediatypeid');
        $Media = new MediaModel();
        if (!intval($mediatypeid)) {
            $mediatypeid = $Media->getMediaTypeByOID($oid);
        }
        $method = $typeArray[strval($mediatypeid)];
        // $url = U('Content/Edit/' . $method);
        // echo '<script>location.href="' . $url . '"</script>';
        $res = array('method' => $method);
        $this->ajaxReturn($res);
    }

    public function getMediaAbsPath() {
        if (!isset($_GET['oid'])) {
            \throw_exception(__METHOD__ . 'get oid failed!');
        }
        $oid = I('get.oid');
        $Path = new PathModel();
        $res = $Path->getMediaAbsPath($oid);
        $this->ajaxReturn($res);
    }

    public function saveMedia() {
        if (!isset($_POST['oid'])) {
            \throw_exception(__METHOD__ . 'post oid failed!');
        }
        $data = I('post.');
        $Media = new MediaModel();
        $this->ajaxReturn($Media->saveMedia($data));
    }

    /**
     * 总集中的分集OID数目
     */
    public function countSeriesEpisodes() {
        if (!isset($_GET['soid'])) {
            $this->ajaxReturn(array('code' => -1, 'data'=>null, 'msg' => 'cannot get series oid'));
        }
        if (!isset($_GET['mediatypeid'])) {
            $this->ajaxReturn(array('code' => -2, 'data'=>null, 'msg' => 'cannot get mediatypeid'));
        }
        $soid = I('get.soid');
        $mediatypeid = intval( I('get.mediatypeid') );

        $n = 0;
        $res = array('code'=>0, 'data'=>$n, 'msg'=>'');
        switch ($mediatypeid) {
            case 5:
                $model = new SeriesepisodeModel();
                $where = array('seriesoid' => $soid);
                $n = $model->where($where)->count();
                $res['data'] = $n;
                break;
            case 6: // 电视节目总集
                $model = new TvshowepisodeModel();
                $where = array('tvshowoid' => $soid);
                $n = $model->where($where)->count();
                $res['data'] = $n;
                break;
            case 8:  // 专题节目总集
                $where = array('specialoid' => $soid);
                $n = M('specialepisode')->where($where)->count();
                $res['data'] = $n;
                break;
            default:
                $res['code'] = -3;
                $res['data'] = -1;
                $res['msg'] = 'not series mediatype 5,6';
        }
        $this->ajaxReturn( $res );
    }

    /**
     * 删除总集 (已经判断没有分集)
     */
    public function deleteSeries() {
        if (!isset($_GET['soid'])) {
            $this->ajaxReturn(array('code' => -1, 'data'=>null, 'msg' => 'cannot get series oid'));
        }
        if (!isset($_GET['mediatypeid'])) {
            $this->ajaxReturn(array('code' => -2, 'data'=>null, 'msg' => 'cannot get mediatypeid'));
        }
        $soid = I('get.soid');
        $mediatypeid = intval( I('get.mediatypeid') );
        $series = new \Content\Model\SeriesModel();
        $n = 0;
        $res = array('code'=>0, 'data'=>$n, 'msg'=>'');
        switch ($mediatypeid) {
            case 5:
                $n = $series->deleteSeries($soid, 'series');
                break;
            case 6:
                $n = $series->deleteSeries($soid, 'tvshow');
                break;
            case 8:   // 专题节目总集
                $n = $series->deleteSeries($soid, 'special');
                break;
            default:
                $res['code'] = -3;
                $res['data'] = -1;
                $res['msg'] = 'not series mediatype 5,6,8';
        }
        $res['data'] = $n;
        unset($series);
        $this->ajaxReturn( $res );
    }

    /**
     * 内容编辑下拉菜单列表赋值
     */
    private function _assignDropdown() {
        // 年份
        $this->assign('years', M('year')->select());
        // 电影/戏剧 类型
        $this->assign('genres', M('genre')->select());
        // 国家/地区
        $this->assign('countries', M('country')->select());
        // 电影语言
        $this->assign('langs', M('language')->select());
        // 标签
        $this->assign('tags', M('tag')->select());
        // 播出频道
        $this->assign('channels', M('channel')->field(array('channelid', 'name'))->select());
        // 附件类型 1: 缩略图, 2:海报
        $this->assign('appendixtypes', M('appendixtype')->select());
    }
}