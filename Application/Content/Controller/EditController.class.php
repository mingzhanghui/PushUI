<?php
/**
 * Created by PhpStorm.
 * User: mzh
 * Date: 2017/1/17
 * Time: 9:14
 */
namespace Content\Controller;
use Content\Model\AppendixModel;
use Content\Model\EditstatusModel;
use Content\Model\MediadiscountModel;
use Content\Model\MedialinkcountryModel;
use Content\Model\MedialinkgenreModel;
use Content\Model\MedialinklanguageModel;
use Content\Model\MedialinktagModel;
use Content\Model\MedialinkyearModel;
use Content\Model\MediaModel;
use Content\Model\MediapriceModel;
use Content\Model\MovieModel;
use Content\Model\PathModel;
use Content\Model\SeriesModel;
use Content\Model\SpecialepisodeModel;
use Content\Model\SpecialModel;
use Home\Model\ConfigureModel;
use Think\App;
use Think\Controller;

use Common\Common\Config;
use Common\Common\Logger;
use Content\Common\File;

// 内容编辑
class EditController extends Controller {

    public function __construct() {
        parent::__construct();
        // ?login
        if (!isset($_SESSION)) {
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

        setcookie("baseurl", __CONTROLLER__, time() + 60 * 60 * 30); // 30 minutes from now on
    }

    // 由方法名 对应 媒体类型ID
    public static $mediatype = array(
        'index' => 1,
        'tvplay' => 2, // 电视剧分集
        'tvprogram' => 3,
        'video' => 4,
        'opera' => 7,
        'special' => 9,
    );

    /**
     * 内容编辑 / 电影
     */
    public function index() {
        // Content\Controller\EditController::index
        $array = explode("::", __METHOD__);
        $method = array_pop($array);
        $mediatypeid = self::$mediatype[$method];
        // 电影媒体列表
        $model = new \Content\Model\PathModel();

        $pagesize = 16;
        $curpage = isset($_GET['p']) ? I('get.p') : 1;
        $mediaList = $model->listMediaByType($mediatypeid, $pagesize, $curpage);

        // dump($todayMediaList);
        $n = count($mediaList);
        for ($i = 0; $i < $n; $i++) {
            $mediaList[$i]['size'] = humansize($mediaList[$i]['size']);
        }
        $mediatype = '电影';
        $totalpage = ceil($model->getMediaCountByType($mediatypeid) / $pagesize);
        $prevpage = ($curpage - 1 < 1) ? 1 : ($curpage - 1);
        $nextpage = ($curpage + 1 > $totalpage) ? $totalpage : ($curpage + 1);

        // media list array
        $this->assign('mediaList', $mediaList);
        // media type movie
        $this->assign('mediatype', $mediatype);
        // pagination
        $this->assign('prevpage', $prevpage);
        $this->assign('curpage', $curpage);
        $this->assign('nextpage', $nextpage);
        $this->assign('totalpage', $totalpage);
        // dump($todayMediaList);

        $this->assignDropdown($model);

        $this->display();
    }

    /**
     * 内容编辑 / 电视剧单集
     */
    public function tvplay() {
        // Content\Controller\EditController::tvplay
        $array = explode("::", __METHOD__);
        $method = array_pop($array);
        $mediatypeid = self::$mediatype[$method];

        $model = new \Content\Model\PathModel();

        $pagesize = 16;
        $curpage = isset($_GET['p']) ? I('get.p') : 1;
        $mediaList = $model->listMediaByType($mediatypeid, $pagesize, $curpage);

        $n = count($mediaList);
        for ($i = 0; $i < $n; $i++) {
            $mediaList[$i]['size'] = humansize($mediaList[$i]['size']);
        }
        $totalpage = ceil($model->getMediaCountByType($mediatypeid) / $pagesize);
        $prevpage = ($curpage - 1 < 1) ? 1 : ($curpage - 1);
        $nextpage = ($curpage + 1 > $totalpage) ? $totalpage : ($curpage + 1);

        // media list array
        $this->assign('mediaList', $mediaList);
        // pagination
        $this->assign('prevpage', $prevpage);
        $this->assign('curpage', $curpage);
        $this->assign('nextpage', $nextpage);
        $this->assign('totalpage', $totalpage);

        $this->assign('mediatype', '电视剧');

        $this->assignDropdown($model);

        $this->display();
    }

    /**
     * 电视剧总集页
     */
    public function tvplayseries() {
        $this->assignDropdown();

        $media = new MediaModel();
        $list = $media->listTVPlaySeriesTitle();
        $this->assign('list', $list);
        $this->display();
    }

    public function listTVPlaySeriesTitle() {
        $model = new \Content\Model\MediaModel();
        $this->ajaxReturn($model->listTVPlaySeriesTitle());
    }

    /**
     * 电视节目 单集页
     */
    public function tvprogram() {
        // Content\Controller\EditController::index
        $array = explode("::", __METHOD__);
        $method = array_pop($array);
        $mediatypeid = self::$mediatype[$method];
        // 电视节目列表

        $pagesize = 16;
        $curpage = isset($_GET['p']) ? I('get.p') : 1;
        $Path = new PathModel();
        $mediaList = $Path->listMediaByType($mediatypeid, $pagesize, $curpage);

        $n = count($mediaList);
        for ($i = 0; $i < $n; $i++) {
            $mediaList[$i]['size'] = File::humansize($mediaList[$i]['size']);
        }
        $mediatype = '电视节目';
        $totalpage = ceil($Path->getMediaCountByType($mediatypeid) / $pagesize);
        $prevpage = ($curpage - 1 < 1) ? 1 : ($curpage - 1);
        $nextpage = ($curpage + 1 > $totalpage) ? $totalpage : ($curpage + 1);

        // media list array
        $this->assign('mediaList', $mediaList);
        // media type movie
        $this->assign('mediatype', $mediatype);
        // pagination
        $this->assign('prevpage', $prevpage);
        $this->assign('curpage', $curpage);
        $this->assign('nextpage', $nextpage);
        $this->assign('totalpage', $totalpage);

        // dump($mediaList);
        // 国家/地区
        $Country = new \Content\Model\CountryModel();
        $countries = $Country->getCountryList();
        $this->assign('countries', $countries);
        // 语言
        $Language = new \Content\Model\LanguageModel();
        $langs = $Language->getLangList();
        $this->assign('langs', $langs);
        // 附件类型 1: 缩略图, 2:海报
        $this->assign('appendixtypes', M('appendixtype')->select());

        $this->display();
    }

    /**
     * 电视节目 总集 页
     */
    public function tvshows() {
        $this->assignDropdown();

        $where = array('mediatypeid' => 6);
        $fields = array('id', 'oid', 'title');
        $list = M('media')->where($where)->field($fields)->select();
        $this->assign('list', $list);

        $this->display();
    }

    /**
     * 刷新电视节目总集标题列表
     */
    public function listTVShowsTitle() {
        $where = array('mediatypeid' => 6);
        $fields = array('id', 'oid', 'title');
        $list = M('media')->where($where)->field($fields)->select();

        $this->ajaxReturn($list);
    }

    /**
     * 热点视频页
     */
    public function video() {
        // Content\Controller\EditController::index
        $array = explode("::", __METHOD__);
        $method = array_pop($array);
        $mediatypeid = self::$mediatype[$method];
        // 电视节目列表
        $model = new \Content\Model\PathModel();

        $pagesize = 16;
        $curpage = isset($_GET['p']) ? I('get.p') : 1;
        $mediaList = $model->listMediaByType($mediatypeid, $pagesize, $curpage);

        $n = count($mediaList);
        for ($i = 0; $i < $n; $i++) {
            $mediaList[$i]['size'] = humansize($mediaList[$i]['size']);
        }
        $mediatype = '热点视频';
        $totalpage = ceil($model->getMediaCountByType($mediatypeid) / $pagesize);
        $prevpage = ($curpage - 1 < 1) ? 1 : ($curpage - 1);
        $nextpage = ($curpage + 1 > $totalpage) ? $totalpage : ($curpage + 1);

        $this->assign('method', $method);
        $this->assign('mediatypeid', $mediatypeid);
        // media list array
        $this->assign('mediaList', $mediaList);
        // media type movie
        $this->assign('mediatype', $mediatype);
        // pagination
        $this->assign('prevpage', $prevpage);
        $this->assign('curpage', $curpage);
        $this->assign('nextpage', $nextpage);
        $this->assign('totalpage', $totalpage);

        // 附件类型 1: 缩略图, 2:海报
        $appendixtypes = M('appendixtype')->select();
        $this->assign('appendixtypes', $appendixtypes);

        $this->display();
    }

    /**
     * 戏曲页
     */
    public function opera() {
        // Content\Controller\EditController::index
        $array = explode("::", __METHOD__);
        $method = array_pop($array);
        $mediatypeid = self::$mediatype[$method];

        $model = new PathModel();

        $pagesize = 16;
        $curpage = isset($_GET['p']) ? I('get.p') : 1;
        $mediaList = $model->listMediaByType($mediatypeid, $pagesize, $curpage);

        $n = count($mediaList);
        for ($i = 0; $i < $n; $i++) {
            $mediaList[$i]['size'] = humansize($mediaList[$i]['size']);
        }

        $mediatype = '戏曲';
        $totalpage = ceil($model->getMediaCountByType($mediatypeid) / $pagesize);
        $prevpage = ($curpage - 1 < 1) ? 1 : ($curpage - 1);
        $nextpage = ($curpage + 1 > $totalpage) ? $totalpage : ($curpage + 1);

        $this->assign('mediatypeid', $mediatypeid);
        // media list array
        $this->assign('mediaList', $mediaList);
        // media type movie
        $this->assign('mediatype', $mediatype);
        // pagination
        $this->assign('prevpage', $prevpage);
        $this->assign('curpage', $curpage);
        $this->assign('nextpage', $nextpage);
        $this->assign('totalpage', $totalpage);

        $this->assignDropdown();

        $this->display();
    }

    /**
     * 专题节目页
     */
    public function special() {
        // Content\Controller\EditController::index
        $array = explode("::", __METHOD__);
        $method = array_pop($array);
        $mediatypeid = self::$mediatype[$method];

        $model = new \Content\Model\PathModel();

        $pagesize = 16;
        $curpage = isset($_GET['p']) ? I('get.p') : 1;
        $mediaList = $model->listMediaByType($mediatypeid, $pagesize, $curpage);

        $n = count($mediaList);
        for ($i = 0; $i < $n; $i++) {
            $mediaList[$i]['size'] = humansize($mediaList[$i]['size']);
        }
        $mediatype = '专题节目';
        $totalpage = ceil($model->getMediaCountByType($mediatypeid) / $pagesize);
        $prevpage = ($curpage - 1 < 1) ? 1 : ($curpage - 1);
        $nextpage = ($curpage + 1 > $totalpage) ? $totalpage : ($curpage + 1);

        $this->assign('mediatypeid', $mediatypeid);
        // media list array
        $this->assign('mediaList', $mediaList);
        $this->assign('mediatype', $mediatype);
        // pagination
        $this->assign('prevpage', $prevpage);
        $this->assign('curpage', $curpage);
        $this->assign('nextpage', $nextpage);
        $this->assign('totalpage', $totalpage);

        $this->assignDropdown();
        $this->display();
    }

    /**
     * 专题节目总集
     */
    public function specialseries() {
        $this->assignDropdown();

        $model = new MediaModel();
        $list = $model->listSpecialSeriesTitle();
        $this->assign('list', $list);

        $this->display();
    }

    /**
     * 内容编辑下拉菜单列表赋值
     */
    public function assignDropdown() {
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

    /**
     * 保存电影草稿
     */
    public function saveMovie() {
        $data = array_filter( I('post.') );
        if (!isset($data['oid'])) {
            E(__METHOD__.': oid is not set!');
        }
        $oid = $data['oid'];
        $where = array('oid' => $oid);

        $Media = new MediaModel();
        $res['media'] = $Media->where($where)->data($data)->save();

        $Path = new PathModel();
        $res['path'] = $Path->where($where)->data(array('asset_name' => $data['title']))->save();

        $Movie = new MovieModel();
        $res['movie'] = $Movie->where($where)->data($data)->save();

        $Editstatus = new EditstatusModel();
        $res['editstatus'] = $Editstatus->setEditStatus($oid, 'editstatus', 1);

        // $links = array('genre', 'tag','year', 'country');
        $this->_setgenreid($oid, $data['genreid']);
        $this->_settagid($oid, $data['tagid']);
        $this->_setyearid($oid, $data['yearid']);
        $this->_setcountryid($oid, $data['countryid']);

        $this->ajaxReturn( $res );
    }

    public function getMovie() {
        $oid = I('get.oid');

        $Movie = new \Content\Model\MovieModel();
        $mov = $Movie->getMovie($oid);

        $Media = new \Content\Model\MediaModel();

        $where = array('oid'=>$oid);
        $fields = array('oid', 'title', 'introduction', 'rating', 'mediatypeid', 'languageid', 'channelid');
        $t = $Media->where($where)->field($fields)->select();
        $res = $t[0];

        // $links = array('genre', 'tag','year', 'country');
        $res['genreid'] = $this->_getgenreid($oid);
        $res['tagid'] = $this->_gettagid($oid);
        $res['yearid'] = $this->_getyearid($oid);
        $res['countryid'] = $this->_getcountryid($oid);

        $Editstatus = new \Content\Model\EditstatusModel();
        $res['slicestatus'] = $Editstatus->getEditStatus($oid, 'slicestatus');

        $this->ajaxReturn( array_merge($mov, $res) );
    }

    public function getAppendixList() {
        if (!isset($_REQUEST['oid'])) {
            \throw_exception(__METHOD__ . 'oid is required!');
        }
        $model = new \Content\Model\AppendixModel();
        $oid = I('oid');
        $res = $model->listAppendixByOID($oid);
        $this->ajaxReturn($res);
    }

    public function getAppendixURLByID() {
        if (!isset($_GET['id'])) {
            \throw_exception(__METHOD__ . 'appendix id is required!');
        }
        $id = I("get.id");
        $where = array("id" => $id);
        $rows = M("appendix")->where($where)->field(array("url"))->select();
        $url = $rows[0]['url'];

        $where = array("name" => "appendix_path");
        $fields = array("stringvalue");
        $rows = M("configure")->where($where)->field($fields)->select();
        $appendix_path = $rows[0]['stringvalue'];
        $len = strlen($_SERVER['DOCUMENT_ROOT']);
        $prefix = substr($appendix_path, $len);

        $url = $prefix . "/" . $url;

        $this->ajaxReturn($url);
    }

    // /PushUI/resource/appendix/
    public function getAppendixURLPrefix() {
        $model = new ConfigureModel();
        $prefix = $model->getAppendixURLPrefix();
        $this->ajaxReturn(array("path" => $prefix));
    }

//array (size=1)
    //'filename' =>
    //array (size=5)
    //'name' => string '255293.jpg' (length=10)
    //'type' => string 'image/jpeg' (length=10)
    //'tmp_name' => string 'D:\software\wamp\tmp\php915E.tmp' (length=32)
    //'error' => int 0
    //'size' => int 109755
    /**
     * 上传附件 缩略图/海报
     * @param string $name   $_FILES[$name]
     * @param null $attach   array("attachoid"=>xxx, "appendixtypeid"=>xxx)
     */
    private function upload($name, $attach) {
        $file = array_filter($_FILES[$name]);

        if (UPLOAD_ERR_NO_FILE == $file['error']) {
            $this->error("No file to upload");
        }
        // isset( $name )
        $file['filename'] = $_FILES[$name];
        unset($_FILES[$name]);
        unset($file['error']);

//    array (size=2)
        //  'attachoid' => string '799379F2E27390788DA1A47B5F7F4A4A' (length=32)
        //  'appendixtypeid' => string '1' (length=1)
        $type = array('image/png', 'image/jpeg', 'image/gif');
        if (!in_array($file['type'], $type)) {
            $this->error('上传的附件不是图片类型!', $_SERVER["HTTP_REFERER"], 3);
        }
        $model = new \Content\Model\AppendixModel();

        if (0 != $model->hasAppendix($attach)) {
            $this->ajaxReturn(array("code" => 1, "msg" => "附件已经存在!"));
        }
        $id = $model->upload($file, $attach);
        if ($id > 0) {
            $this->ajaxReturn(array("code" => 0, "msg" => "附件上传成功"));
        }
        echo "<script>console.log('附件上传失败!');</script>";
        return -1;
    }

    /**
     * xhr上传图片
     * @return int
     */
    public function xhrupload() {
        $file = array_filter($_FILES['filename']);
        unset($file['error']);

//    array (size=2)
        //  'attachoid' => string '799379F2E27390788DA1A47B5F7F4A4A' (length=32)
        //  'appendixtypeid' => string '1' (length=1)
        $type = array('image/png', 'image/jpeg', 'image/gif');
        if (!in_array($file['type'], $type)) {
            $this->error('上传的附件不是图片类型:'.$type, $_SERVER["HTTP_REFERER"], 3);
        }
        $appendix = new AppendixModel();

        $attach = array(
            'appendixtypeid' => I('post.appendixtypeid'),
            'attachoid'      => I('post.attachoid')
        );
        // dump( $attach ); die;
        if (0 != $appendix->hasAppendix($attach)) {
            $this->ajaxReturn(array("code" => 1, "msg" => "附件已经存在!"));
        }
        $id = $appendix->upload($file, $attach);
        if ($id > 0) {
            $this->ajaxReturn(array("code" => 0, "msg" => "附件上传成功"));
        }

        echo "<script>console.error('附件上传失败!');</script>";
        return -1;
    }

    /**
     * 专题节目总集 图片上传, 替换原有图片
     */
    public function xhrUploadOverwrite() {
        $file = array_filter($_FILES['filename']);
        unset($file['error']);

        $type = array('image/png', 'image/jpeg', 'image/gif');
        if (!in_array($file['type'], $type)) {
            $this->ajaxReturn(array("code" => -2, "id" => 0, "msg" => 'Unexpected uploaded image type!'));
        }
        $Appendix = new \Content\Model\AppendixModel();

        $attach = array(
            'attachoid' => I('post.attachoid'),
            'appendixtypeid' => I('post.appendixtypeid'),
        );

        // 附件已经存在, 先删除附件
        if ($id = $Appendix->hasAppendix($attach)) {
            $Appendix->removeAppendix($id);
        }
        $id = $Appendix->upload($file, $attach);
        if ($id > 0) {
            $this->ajaxReturn(array("code" => 0, "id" => $id, "msg" => "upload success"));
        }
        $this->ajaxReturn(array("code" => -1, "id" => 0, "msg" => "unknown upload failure"));
    }

    /**
     * 通过表单上传图片
     * @return int
     */
    public function formUpload() {
        $file = array_filter($_FILES['filename']);
        unset($file['error']);

//    array (size=2)
        //  'attachoid' => string '799379F2E27390788DA1A47B5F7F4A4A' (length=32)
        //  'appendixtypeid' => string '1' (length=1)
        $type = array('image/png', 'image/jpeg', 'image/gif');
        if (!in_array($file['type'], $type)) {
            $this->error('上传的附件不是图片类型!', $_SERVER["HTTP_REFERER"], 3);
        }
        $model = new \Content\Model\AppendixModel();

        $attach['attachoid'] = I('post.attachoid');
        $attach['appendixtypeid'] = I('post.appendixtypeid');

        if (0 != $model->hasAppendix($attach)) {
            $this->error("附件已经存在!");
            return 2;
        }
        $id = $model->upload($file, $attach);
        if ($id > 0) {
            // $this->success("附件上传成功");
            echo "<script>history.go(-1)</script>";
            return 0;
        }

        $this->error('附件上传失败!');
        return 1;
    }

    /**
     * 删除附件
     */
    public function removeAppendix() {
        if (!isset($_GET['id']) && !isset($_POST['id'])) {
            \throw_exception(__METHOD__ . ': Appendix id is required!');
        }
        $model = new AppendixModel();
        $id = I('id');
        $this->ajaxReturn($model->removeAppendix($id));
    }

    /**
     * 发送备播请求
     */
    public function cut() {
        if (!isset($_GET['oid'])) {
            \throw_exception(__METHOD__ . ': OID is required!');
        }
        $oid = strtoupper( I('get.oid') );
        $where = array('oid' => $oid);

        $EditStatus = new \Content\Model\EditstatusModel();
        $sliceStatus = $EditStatus->getEditStatus($oid, 'slicestatus');

        // 已经备播 不再切分
        if ($sliceStatus == 1) {
            $cutRes = array('code'=>2, 'msg'=>'已经备播 不再切分');
            $this->_logxhrReturn($oid, $cutRes);
        }

        // 如果drm文件已经存在，先删除
        $Conf = new \Home\Model\ConfigureModel();
        // drm文件所在目录
        $content_path = rtrim( $Conf->getconfstrbyname('content_path'), '\\\/' );

        $Path = new \Content\Model\PathModel();
        $t = $Path->where($where)->field('url')->select();
        $url = $t[0]['url'];
        unset($t);
        $drm_path = $content_path . '/drm_' . $url;
        // 源文件路径
        $cutRes['path'] = $content_path . '/' . $url;

        if (file_exists($drm_path)) {
            $d = @unlink($drm_path);
            if (!$d) {
                $cutRes['code'] = '-6';
                $cutRes['msg'] = 'drm文件'.$drm_path.'已经存在,且无法删除';
                $this->_logxhrReturn($oid, $cutRes);
            }
        }
        // 确定part路径存在
        $part_path = rtrim($Conf->getconfstrbyname('part_path'), '\\/');
        if (!is_dir($part_path)) {
            @unlink($part_path);
            mkdir($part_path, 0777, true);
        }
        $part = $part_path . '/' . $oid;
        if (is_dir($part)) {
            File::emptydir($part);
            rmdir($part);
        }
        // part文件包括oid路径
        $cutRes['part'] = $part;

        // set_time_limit(90);
        // ui-server 发送备播请求
        $Server = new \Home\Common\Server();
        $res = $Server->cutPart($oid);
        $cutRes['code'] = $res['code'];
        $cutRes['msg'] = $res['msg'];
        unset($res);
        if ($cutRes['code'] !== 0) {
            $this->_logxhrReturn($oid, $cutRes);
        }

        $this->ajaxReturn($cutRes);
    }

    /**
     * 检查切分状态 通过文件数
     */
    public function cutStatus() {
        $path = I('post.path');   // 内容目录
        $partdir = I('post.part');

        $res = array();
        $res['partdir'] = $partdir;

        $filesize = File::realFileSize($path);

        define("PART_SIZE", 524638);
        $parts = ceil($filesize / PART_SIZE);

        if (is_dir($partdir)) {
            $curParts = File::countDir($partdir);
            $res['data'] = array(
                'total' => $parts,
                'done'  => $curParts
            );

            if ($curParts < $parts) {
                $res['code'] = 2;
                $res['msg'] = '正在切分文件';

            } else {
                $res['code'] = 1;
                $res['msg'] = '文件切分完了';
            }
        } else {
            $res['code'] = 2;
            $res['data'] = array(
                'total' => $parts,
                'done'  => 0
            );
            $res['msg'] = '正在生成drm文件';
        }
        $this->ajaxReturn($res);
    }

    /**
     * 写入log并ajaxReturn
     * @param $oid
     * @param $a
     */
    private function _logxhrReturn($oid, $a) {
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write(
                sprintf('内容编辑/备播: [%s] %s', $oid, json_encode($a)),
                $_SESSION['username']
            );
        }
        $this->ajaxReturn($a);
    }

    /**
     * 检查备播状态
     */
    public function getSliceStatus() {
        if (!isset($_POST['oid']) && !isset($_GET['oid'])) {
            \throw_exception(__METHOD__ . 'OID is required!');
        }
        $es = new \Content\Model\EditstatusModel();
        $oid = I('oid');
        $res = $es->getEditStatus($oid, 'slicestatus');
        $this->ajaxReturn($res);
    }
    /**
     * 媒体文件提交
     */
    public function submitMedia() {
        if (!isset($_POST['oid'])) {
            \throw_exception(__METHOD__ . 'OID is required!');
        }
        $model = new EditstatusModel();
        $oid = I('oid');
        $config = \Common\Common\Config::getInstance();
        if ($config->hasReviewed()) {
            $res = $model->setEditStatus($oid, 'editstatus', 2);
        } else {
            $res = $model->setEditStatus($oid, 'editstatus', 3);
        }

        $this->ajaxReturn($res);
    }

    /**
     * 删除分集
     * @return int
     */
    public function delContent() {
        if (!isset($_GET['oid']) || !isset($_GET['mediatypeid'])) {
            \throw_exception(__METHOD__ . 'OID or mediatypeid is not set!');
        }

        $OID = I('get.oid');
        $MediaTypeID = intval(I('get.mediatypeid'));

        $seriesMediaTypes = array(5, 6, 8);
        if (in_array($MediaTypeID, $seriesMediaTypes)) {
            $this->ajaxReturn(array('code' => 1, 'msg' => '总集请在内容编辑页删除!'));
        }

        // delete appendix files & directory
        $Appendix = new AppendixModel();
        $Appendix->where(array('attachoid' => $OID))->delete();  // delete from db
        $Appendix->delAllAppendix($OID);                         // delete from disk

        $where = array(
            "oid" => $OID,
        );
        $model = new MediaModel();
        $model->where($where)->delete();

        $path = new PathModel();
        $path->where($where)->delete();
        unset($path);
        $editstatus = new EditstatusModel();
        $editstatus->delete($where);
        unset($editstatus);

        $price = new MediapriceModel();
        $price->delete($where);
        unset($price);

        $discount = new MediadiscountModel();
        $discount->delete($where);
        unset($discount);

        switch ($MediaTypeID) {
            case 1:
                $movie = new MovieModel();
                $movie->where($where)->delete();
                $model->delMedia('medialinkgenre', $where);
                $model->delMedia('medialinkyear', $where);
                $model->delMedia('medialinktag', $where);
                $model->delMedia('medialinkcountry', $where);
                break;
            case 2:
                $where['episodeoid'] = $where['oid'];
                unset($where['oid']);
                $model->delMedia("seriesepisode", $where);
                break;
            // 电视节目分集
            case 3:
                $model->delMedia("tvshowepisode", array("episodeoid" => $OID));
                break;
            case 4: $model->delMedia("video", $where);break;
            // 电视剧总集
            case 5:
                $model = new SeriesModel(); $model->deleteSeries($OID, "series"); break;
            // 电视节目总集
            case 6:
                $model = new SeriesModel(); $model->deleteSeries($OID, "tvshow"); break;
            case 7:
                $model->delMedia("opera", $where);
                $model->delMedia("medialinkgenre", $where);
                $model->delMedia("medialinkyear", $where);
                $model->delMedia("medialinktag", $where);
                $model->delMedia("medialinkcountry", $where);
                break;
            case 8:   //  专题节目总集
                $model = new SeriesModel(); $model->deleteSeries($OID, "special"); break;
                break;
            case 9: //  专题节目分集
                $where['episodeoid'] = $where['oid'];
                unset($where['oid']);
                $model->delMedia("specialepisode", $where);
                break;
            default:
                throw_exception("Unexpected Media Type ID: " . $MediaTypeID);
        }
        //      删除 其他跟oid关联的表(业务期内容, 播发状态)
        $link = new \Subscribe\Model\MissionlinkmediaModel();
        $link->where(array('mediaoid' => $OID))->delete();
        unset($link);
        $ps = new \Pushctrl\Model\PushstatusModel();
        $ps->where(array('oid' => $OID))->delete();
        unset($ps);

        M()->execute('vacuum');
        return 0;
    }
//  end function delContent

    public function newsoid() {
        $model = new \Content\Model\MediaModel();
        $soid = $model->generateSeriesOID();
        setcookie("soid", $soid, time() + 60 * 60 * 30);
        $this->ajaxReturn($soid);
    }

    /**
     * 添加新的电视剧 总集
     */
    public function newTVSeries() {
        $Media = new MediaModel();
        $oid = null;
        if (!isset($_POST["oid"])) {
            $oid = $Media->generateSeriesOID();
        }
        $title = I('title');

        $Series = new SeriesModel();
        $lastid = array();
        // media表
        $data = array(
            'oid' => $oid,
            'title' => $title,
            'introduction' => I('introduction'),
            'rating' => I('rating'),
            'mediatypeid' => 5,
            'languageid' => I('languageid'),
        );
        $where = array(
            'title' => $title
        );
        $count = $Media->where($where)->count();
        if ($count > 0) {
            $this->ajaxReturn(array('code' => (-1)*$count, 'msg' => '总集名"' . $title . '"已经重复，请重新命名', 'oid' => ''));
        }
        $lastid[] = $Media->data($data)->add();
        unset($data);

        $data = array(
            'oid' => $oid,
            'episodes' => I('episodes'),
            'director' => I('director'),
            'actor' => I('actor'),
        );
        $lastid[] = $Series->data($data)->add();
        unset($data);

        $data = array(
            'oid' => $oid,
            'yearid' => I('yearid'),
        );
        $model = new MedialinkyearModel();
        $lastid[] = $model->setyear($data);
        unset($data['yearid']);

        $data['genreid'] = I('genreid');
        if ($data['genreid']) {
            $model = new MedialinkgenreModel();
            $lastid[] = $model->setGenre($data);
        }
        unset($data['genreid']);

        $data['countryid'] = I('countryid');
        if ($data['countryid']) {
            $model = new MedialinkcountryModel();
            $lastid[] = $model->setcountry($data);
        }
        unset($data['countryid']);

        $data['languageid'] = I('languageid');
        if ($data['languageid']) {
            $model = new MedialinkcountryModel();
            $lastid[] = $model->data($data)->add();
        }
        unset($data['languageid']);

        $data['tagid'] = I('tagid');
        if ($data['tagid']) {
            $model = new MedialinktagModel();
            $lastid[] = $model->data($data)->add();
        }

        $res = true;
        foreach ($lastid as $id) {
            $res = $res && $id;
        }
        if ($res) {
            $data = array(
                "attachoid" => $oid,
            );
            if (isset($_FILES['NewSeriesThumbAppendix'])) {
                // S2016122800000000000000000000491	2	s2016122800000000000000000000491/e02.jpg	e02.jpg	12486
                // S2016122800000000000000000000491	1	s2016122800000000000000000000491/p04_thumb_tv0001.jpg	p04_thumb_tv0001.jpg	20692
                $data['appendixtypeid'] = 1; // 缩略图
                UPLOAD_ERR_OK == $_FILES['NewSeriesThumbAppendix']['error'] &&
                $this->upload('NewSeriesThumbAppendix', $data);
            }
            if (isset($_FILES['NewSeriesBackgroundAppendix'])) {
                $data['appendixtypeid'] = 2; // 海报

                UPLOAD_ERR_OK == $_FILES['NewSeriesThumbAppendix']['error'] &&
                $this->upload('NewSeriesBackgroundAppendix', $data);
            }
        }

        $this->ajaxReturn(array('code' => 0, 'msg' => '总集添加成功!', 'oid' => $oid));
    }

    /**
     * 按片名查找电视剧总集
     */
    public function listTVPlaySeries() {
        $querystring = I('q');
        $cond = array();
        if (isset($querystring) && $querystring != '') {
            $cond['title'] = array("LIKE", '%' . $querystring . '%');
        }
        $model = new \Content\Model\SeriesModel();
        $this->ajaxReturn($model->listTVPlaySeries($cond));
    }

    /**
     * 电视剧分集保存草稿
     */
    public function saveEpisodeDraft() {
        if (!isset($_POST['episodeoid']) || !isset($_POST['seriesoid'])) {
            throw_exception(__METHOD__ . " episodeoid and seriesoid are required");
        }

        $data = array_filter( I('post.') );
        $oid = $data['episodeoid'];

        $path = new PathModel();
        $path->where(array('oid' => $oid))->data(array('asset_name' => $data['episodetitle']))->save();
        // SeriesEpisode 中是否存在这个 EpisodeOID
        $soid = $data['seriesoid'];

        // 电视剧单集名称
        $title = $data['episodetitle'];
        $episodeindex = $data['episodeindex'];
        $runtime = $data['runtime'];
        $introduction = $data['introduction'];

        $SeriesEpisode = new \Content\Model\SeriesepisodeModel();
        $where = array("episodeoid" => $oid);
        $count = $SeriesEpisode->where($where)->count();

        $EpisodeData = array(
            "seriesoid" => $soid,
            "title" => $title,
            "runtime" => $runtime,
            "episodeindex" => $episodeindex,
        );

        $result = array("rows" => 0, "lastid" => 0);
        if ($count > 0) {
            $result['rows'] = $SeriesEpisode->data($EpisodeData)->where($where)->save();
        } else {
            $EpisodeData['episodeoid'] = $oid;
            $result['lastid'] = $SeriesEpisode->data($EpisodeData)->add();
        }

        $Series = new SeriesModel();
        $where = array("oid" => $soid);
        $SeriesData = array(
            "Episodes" => $data['episodes'],
        );
        $Series->data($SeriesData)->where($where)->save();

        // 分集简介
        $where = array("oid" => $oid);
        $data = array(
            "introduction" => $introduction,
            'title'        => $title
        );
        $media = new MediaModel();
        $media->where($where)->data($data)->save();

        $EditStatus = new \Content\Model\EditstatusModel();
        $EditStatus->setEditStatus($oid, 'editstatus', 1);

        $this->ajaxReturn( $result );
    }

    /**
     * 电视剧单集详情
     */
    public function TVPlayEpisodeInfo() {
        if (!isset($_POST['oid'])) {
            throw_exception(__METHOD__ . " it requires oid");
        }
        $model = new \Content\Model\SeriesepisodeModel();
        $oid = I("oid");
        $this->ajaxReturn($model->TVPlayEpisodeInfo($oid));
    }

    /**
     * 电视剧 总集中已经添加的分集数
     */
    public function countSeriesEpisode() {
        if (!isset($_GET['soid'])) {
            throw_exception(__METHOD__ . " it requires soid");
        }
        $se = new \Content\Model\SeriesepisodeModel();
        $this->ajaxReturn($se->countSeriesEpisode(I('get.soid')));
    }

    /**
     * 电视节目 总集中已经添加的分集数
     */
    public function countShowsEpisode() {
        if (!isset($_GET['soid'])) {
            throw_exception(__METHOD__ . " it requires soid");
        }
        $model = new \Content\Model\TvshowepisodeModel();
        $this->ajaxReturn($model->countShowsEpisode(I('get.soid')));
    }

    /**
     * 专题节目 总集中已经添加的分集数
     */
    public function countSpecialEpisode() {
        if (!isset($_GET['soid'])) {
            throw_exception(__METHOD__ . " it requires soid");
        }
        $model = new \Content\Model\SpecialepisodeModel();
        $this->ajaxReturn($model->countSpecialEpisode(I('get.soid')));
    }

    /**
     * 删除总集
     */
    public function deleteSeries() {
        if (!isset($_GET['soid'])) {
            throw_exception(__METHOD__ . " it requires soid");
        }
        $model = new \Content\Model\SeriesModel();
        $soid = I('get.soid');
        $type = I('get.type');
        $this->ajaxReturn($model->deleteSeries($soid, $type));
    }

    /**
     * 点击电视剧总集列表响应对应所有内容
     */
    public function respTVSeries() {
        if (!isset($_GET['soid'])) {
            throw_exception(__METHOD__ . " could not get soid");
        }
        $oid = I('soid');
        $result = array(
            'series' => D('series')->getTVPlaySeriesInfo($oid),
            'episodes' => D('seriesepisode')->listTVPlayEpisodesBySoid($oid),
            'appendix' => D('appendix')->listAppendixByOID($oid),
        );
        $this->ajaxReturn($result);
    }

    /**
     * 点击 电视节目 总集列表响应对应所有内容
     */
    public function respTVShows() {
        if (!isset($_GET['soid'])) {
            throw_exception(__METHOD__ . " could not get soid");
        }
        $soid = I('soid');

        $Tvshow = new \Content\Model\TvshowModel();
        $Tvshowepisode = new \Content\Model\TvshowepisodeModel();
        $Appendix = new \Content\Model\AppendixModel();

        $result = array(
            'shows' => $Tvshow->getTVShowsInfo($soid),
            'episodes' => $Tvshowepisode->listTVShowEpisodeBySoid($soid),
            'appendix' => $Appendix->listAppendixByOID($soid),
        );
        $this->ajaxReturn($result);
    }

    /**
     * 修改电视剧总集
     */
    public function editTvPlaySeries() {
        if (!isset($_GET['soid'])) {
            throw_exception(__METHOD__ . " could not get soid");
        }
        $data = I('get.');
        $soid = $data['soid'];
        unset($data['soid']);
        $model = new \Content\Model\SeriesModel();
        $this->ajaxReturn($model->editTvPlaySeries($data, $soid));
    }

    /**
     * 保存修改电视节目总集
     */
    public function editTvShowSeries() {
        if (!isset($_POST['soid'])) {
            throw_exception(__METHOD__ . " could not post soid");
        }
        $data = I('post.');
        $soid = $data['soid'];
        unset($data['soid']);
        $model = new \Content\Model\TvshowModel();
        $this->ajaxReturn($model->editTvShowSeries($data, $soid));
    }

    /**
     * 添加新的电视节目总集
     */
    public function newTVShows() {
        // dump(I("post."));
        $media = new \Content\Model\MediaModel();
        $oid = null;
        if (!isset($_POST["oid"])) {
            $oid = $media->generateSeriesOID();
        }
        $title = I('title'); // media表中利用
        $mediatypeid = 6; // 电视节目总集

        $language = I('post.languageid');

        // Media表中信息
        $data = array(
            'oid' => $oid,
            'title' => $title,
            'rating' => I('post.rating'),
            'introduction' => I('post.introduction'),
            'languageid' => $language,
            'mediatypeid' => $mediatypeid, // 电视节目总集
        );
        $media->data($data)->add();
        unset($data);

        $where = array('oid' => $oid);
        // TVShow表中信息
        $data = array(
            'oid' => $oid,
            'host' => I('post.host'),
            'sourcefrom' => I('post.sourcefrom'),
            'tvshowtype' => I('post.tvshowtype'),
        );
        $media->addMediaUpdate("tvshow", $data, $where);
        unset($data);

        // MedialinkCountry表中信息
        $data = array(
            "oid" => $oid,
            "countryid" => I('post.countryid'),
        );
        $Country = new MedialinkcountryModel();
        $Country->setcountry($data);
        unset($data);

        // 语言
        $data = array(
            'oid' => $oid,
            'languageid' => $language
        );
        $Language = new MedialinklanguageModel();
        $Language->setLang($data);
        unset($data);

        // upload attachment
        $data = array(
            "attachoid" => $oid,
        );
        if (isset($_FILES['NewSeriesThumbAppendix'])) {
            // S2016122800000000000000000000491	2	s2016122800000000000000000000491/e02.jpg	e02.jpg	12486
            // S2016122800000000000000000000491	1	s2016122800000000000000000000491/p04_thumb_tv0001.jpg	p04_thumb_tv0001.jpg	20692
            $data['appendixtypeid'] = 1; // 缩略图
            UPLOAD_ERR_OK == $_FILES['NewSeriesThumbAppendix']['error'] &&
            $this->upload('NewSeriesThumbAppendix', $data);
        }
        if (isset($_FILES['NewSeriesBackgroundAppendix'])) {
            $data['appendixtypeid'] = 2; // 海报

            UPLOAD_ERR_OK == $_FILES['NewSeriesThumbAppendix']['error'] &&
            $this->upload('NewSeriesBackgroundAppendix', $data);
        }

        $this->ajaxReturn(array('code' => 0, 'msg' => '总集添加成功!', 'oid' => $oid));
    }

    /**
     * 查找电视节目总集 列表
     */
    public function listTVShows() {
        $querystring = I('q');
        $cond = array();
        if (isset($querystring) && $querystring != '') {
            $cond['title'] = array("LIKE", '%' . $querystring . '%');
        }
        $model = new \Content\Model\MediaModel();
        $this->ajaxReturn($model->listTVShows($cond));
    }

    /**
     * 电视节目单集详情
     */
    public function TVShowEpisodeInfo() {
        if (!isset($_GET['oid'])) {
            throw_exception(__METHOD__ . " it requires oid");
        }
        $model = new \Content\Model\TvshowepisodeModel();
        $oid = I("get.oid");
        $this->ajaxReturn($model->TVShowEpisodeInfo($oid));
    }

    /**
     * 保存电视节目单集信息, POST oid
     */
    public function saveTVShowEpisode() {
        if (!isset($_POST['oid'])) {
            throw_exception(__METHOD__ . " it requires POST oid");
        }
        $model = new \Content\Model\TvshowepisodeModel();
        $post = I("post.");
        $this->ajaxReturn($model->saveTVShowEpisode($post));
    }

    /**
     * 编辑热点视频基本内容
     */
    public function setVideoInfo() {
        if (!isset($_POST['oid'])) {
            throw_exception(__METHOD__ . " it requires post oid");
        }
        $result = array();
        $oid = I('post.oid');
        $title = I('post.title');

        $data = array(
            "title" => $title,
            "resource" => I('post.resource'),
            "bftime" => I('post.bftime'),
            "introduction" => I('post.introduction'),
        );
        $Video = new \Content\Model\VideoModel();
        $where = array("oid" => $oid);
        $result['video'] = $Video->where($where)->data($data)->save();

        unset($data['resource']);
        unset($data['bftime']);

        $data['mediatypeid'] = 4;

        $Media = new \Content\Model\MediaModel();
        if ($Media->where($where)->count() > 0) {
            $result['media'] = $Media->where($where)->data($data)->save();
        } else {
            $data['oid'] = $oid;
            $result['media'] = $Media->add($data);
        }

        $path = new PathModel();
        $path->where($where)->data(array('asset_name' => $title))->save();

        $model = new \Content\Model\EditstatusModel();
        $editstatus = $model->getEditStatus($oid, 'editstatus');
        if (!isset($editstatus) || $editstatus < 1) {
            $result['editstatus'] = $model->setEditStatus($oid, 'editstatus', 1);
        }

        $this->ajaxReturn($result);
    }

    public function getVideoInfo() {
        if (!isset($_GET['oid'])) {
            throw_exception(__METHOD__ . " it requires GET oid");
        }
        $model = new \Content\Model\VideoModel();
        $oid = I("get.oid");
        $this->ajaxReturn($model->getVideoInfo($oid));
    }

    public function getOperaInfo() {
        if (!isset($_GET['oid'])) {
            throw_exception(__METHOD__ . " it requires GET oid");
        }
        $model = new \Content\Model\OperaModel();
        $oid = I("get.oid");
        $this->ajaxReturn($model->getOperaInfo($oid));
    }

    public function setOperaInfo() {
        if (!isset($_POST['oid'])) {
            throw_exception(__METHOD__ . " it requires post oid");
        }
        $model = new \Content\Model\OperaModel();
        $this->ajaxReturn($model->setOperaInfo(I('post.')));
    }

    /**
     * 新增专题节目总集
     */
    public function newSpecialSeries() {
        if (!isset($_POST['soid'])) {
            throw_exception(__METHOD__ . " it requires post soid");
        }
        $special = new \Content\Model\SpecialModel();
        $post = I("post.");
        $this->ajaxReturn($special->newSpecialSeries($post));
    }

    /**
     * 新增属性name (value is TO BE DETERMINED)
     */
    public function newAttr() {
        if (!isset($_GET['name']) || trim($_GET['name']) == "") {
            throw_exception(__METHOD__ . " GET name is required");
        }
        $Attr = M("attr");
        $name = I("get.name");
        $data = array("name" => $name);
        $rows = $Attr->field(array("id", "name"))->where($data)->select();
        if (is_null($rows[0])) {
            $code = $Attr->data($data)->add();
            $id = $Attr->getLastInsID();
            $result = array("code" => $code, "id" => $id, "name" => $name, "msg" => sprintf("add attr %s.", $name));
        } else {
            $id = $rows[0]["id"];
            $result = array("code" => 0, "id" => $id, "name" => $name, "msg" => "attr name has already exists!");
        }
        $this->ajaxReturn($result);
    }

    /**
     * 按专题节目名查找专题节目
     */
    public function listSpecialSeries() {
        $q = I('q');
        $cond = array();
        if (isset($q) && $q != '') {
            $cond['title'] = array("LIKE", '%' . $q . '%');
        }
        $model = new \Content\Model\MediaModel();

        $this->ajaxReturn($model->listSpecialSeries($cond));
    }

    /**
     * 设置专题节目分集
     * array(
     * 'oid'  => xxx,
     * 'soid' => xxx,
     * 'title' => xxx,
     * 'introduction' => xxx,
     * 'episodeindex' => xxx
     * 'attrs' => array(
     *    0 => [5, '数学']
     *    1 => [4, 'php'],
     *   ...
     *  );
     *);
     */
    public function setSpecialEpisodeInfo() {
        if (!isset($_POST['oid']) || !isset($_POST['soid'])) {
            throw_exception(__METHOD__ . " oid and soid are required");
        }
        $model = new \Content\Model\SpecialepisodeModel();
        $this->ajaxReturn($model->setSpecialEpisodeInfo(I("post.")));
    }

    /**
     * 修改专题节目总集
     * array(
     * 'soid'  => xxx,
     * 'title' => xxx,
     * 'introduction' => xxx,
     * 'languageid' => xxx,
     * 'countryid' => xxx,
     * 'genreid'  => xxx,
     * 'attrs' => array(
     *    0 => [5, '数学']
     *    1 => [4, 'php'],
     *   ...
     * );
     */
    public function setSpecialsInfo() {
        if (!isset($_POST['soid'])) {
            throw_exception(__METHOD__ . " soid is required");
        }
        $model = new \Content\Model\MediaModel();
        $this->ajaxReturn($model->setSpecialSeriesInfo(I("post.")));
    }

    /**
     * 取得专题节目单集信息
     * 没有设置 title就取文件名
     * @return array (
     *  'info' => array(
     *  'attrs' => array(
     *     0 => array('id'=>xx, 'name'=>xx, 'value'=>xx),
     *     1 => array('id'=>xx, 'name'=>xx, 'value'=>xx)
     *   )
     *  'episodeindex' => xxx,
     *  'introduction' => xxx,
     *  'specialoid' => xxx,
     *  'stitle'    => xxx,
     *  'title'    => xxx
     * ),
     * 'pic' => array(),
     * 'cut' => 1/0,
     */
    public function getSpecialInfo() {
        if (!isset($_GET['oid'])) {
            throw_exception(__METHOD__ . " oid is required");
        }
        $se = new SpecialepisodeModel();
        $appendix = new AppendixModel();
        $es = new EditstatusModel();

        $oid = I("get.oid");
        $result = array(
            'info' => $se->getSpecial($oid),
            'pic' => $appendix->listAppendixByOID($oid),
            'cut' => $es->getEditStatus($oid, 'slicestatus'),
        );
        $this->ajaxReturn($result);
    }

    /**
     * 取得专题节目总集信息
     */
    public function getSpecialSeriesInfo() {
        if (!isset($_GET['soid'])) {
            throw_exception(__METHOD__ . " soid is required");
        }
        $model = new \Content\Model\SpecialModel();
        $soid = I("get.soid");
        $result = $model->getSpecialSeriesInfo($soid);
        $this->ajaxReturn($result);
    }

    /**
     * 生成内容二维码到页面 /content/movie/info?objid=xxx
     */
    public function mediaQRCode() {
        $oid = I('get.oid');
        $type = I('get.type');

        $uri = '/content/'.$type.'/info?objid='.$oid;
        // $url = C('QR_URL') . $uri . C('QR_SUFFIX');
        $url = $this->qrURL($uri);
        // Vendor('phpqrcode.phpqrcode');
        include './ThinkPHP/Extend/Vendor/phpqrcode/phpqrcode.php';
        $errorCorrectionLevel = 3; // 容错级别
        $matrixPointSize = 1;      // 生成图片大小
        // 生成二维码图片
        $object = new \QRcode();
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 0);
    }

    /**
     * 取得二维码图片url
     */
    public function getQrCode() {
        $oid = I('get.oid');
        $type = I('get.type');
        $url = __ROOT__ .'/resource/qrcode/'.strtoupper($oid).'/qrcode.png';

        $path = $this->_qrpath($oid);
        if (file_exists($path)) {
            $this->ajaxReturn(array('url' => $url));
        } else {
            $res = array(
                'url' => $url,
                'path' => $this->doSaveQrCode($oid, $type)
            );
            $this->ajaxReturn($res);
        }
    }

    private function _setgenreid($oid, $value) {
        $Model = new \Content\Model\MedialinkgenreModel();
        $where = array('oid' => $oid);
        $data = array('genreid' => $value);
        $t = $Model->field('oid')->where($where)->count();
        if ($t == 0) {  // insert
            $data['oid'] = $oid;
            $n = $Model->data($data)->add();
        } else {             // update
            $n = $Model->where($where)->data($data)->save();
        }
        return $n;
    }
    private function _getgenreid($oid) {
        $Model = new \Content\Model\MedialinkgenreModel();
        $res = $Model->where(array('oid'=>$oid))->field('genreid')->select();
        return $res[0]['genreid'];
    }
    private function _settagid($oid, $value) {
        $Model = new \Content\Model\MedialinktagModel();
        $where = array('oid' => $oid);
        $data = array('tagid' => $value);
        $t = $Model->field('oid')->where($where)->count();
        if ($t == 0) {  // insert
            $data['oid'] = $oid;
            $n = $Model->data($data)->add();
        } else {             // update
            $n = $Model->where($where)->data($data)->save();
        }
        return $n;
    }
    private function _gettagid($oid) {
        $Model = new \Content\Model\MedialinktagModel();
        $res = $Model->where(array('oid'=>$oid))->field('tagid')->select();
        return $res[0]['tagid'];
    }
    private function _setyearid($oid, $value) {
        $Model = new \Content\Model\MedialinkyearModel();
        $where = array('oid' => $oid);
        $data = array('yearid' => $value);
        $t = $Model->field('oid')->where($where)->count();
        if ($t == 0) {  // insert
            $data['oid'] = $oid;
            $n = $Model->data($data)->add();
        } else {             // update
            $n = $Model->where($where)->data($data)->save();
        }
        return $n;
    }
    private function _getyearid($oid) {
        $Model = new \Content\Model\MedialinkyearModel();
        $res = $Model->where(array('oid'=>$oid))->field('yearid')->select();
        return $res[0]['yearid'];
    }
    private function _setcountryid($oid, $value) {
        $Model = new \Content\Model\MedialinkcountryModel();
        $where = array('oid' => $oid);
        $data = array('countryid' => $value);
        $t = $Model->field('oid')->where($where)->count();
        if ($t == 0) {  // insert
            $data['oid'] = $oid;
            $n = $Model->data($data)->add();
        } else {             // update
            $n = $Model->where($where)->data($data)->save();
        }
        return $n;
    }
    private function _getcountryid($oid) {
        $Model = new \Content\Model\MedialinkcountryModel();
        $res = $Model->where(array('oid'=>$oid))->field('countryid')->select();
        return $res[0]['countryid'];
    }

    /**
     * 保存内容二维码 /content/movie/info?objid=xxx
     */
    public function saveQrCode() {
        $oid = strtoupper( I('get.oid') );
        $type = I('get.type');

        $url = '';
        $path = $this->doSaveQrCode($oid, $type, $url);

        $this->ajaxReturn(
            array(
                'path'=>$path,
                'dir' => __ROOT__ .'/resource/qrcode/'.$oid.'/qrcode.png',
                'url' => $url
            )
        );
    }

    /**
     * 内容信息页 qrcode uri => qrcode
     * @param $oid
     * @param $type
     * @param $url
     * @return string
     */
    private function doSaveQrCode($oid, $type, &$url) {
        $typeid = intval($type);
        if ($typeid != 0) {
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
        }
        $uri = '/content/'.$type.'/info?objid='.$oid;
        $url = $this->qrURL($uri);
        // echo $url; die;
        \Think\Log::write('qrcode url for ['.$oid.'] '.$url);

        $path = $this->_qrpath($oid);
        if (file_exists($path)) {
            unlink($path);
        }

        include './ThinkPHP/Extend/Vendor/phpqrcode/phpqrcode.php';
        $errorCorrectionLevel = 3; // 容错级别
        $matrixPointSize = 1;      // 生成图片大小 85*85
        // 生成二维码图片
        $object = new \QRcode();
        $object->png($url, $path, $errorCorrectionLevel, $matrixPointSize, 2);
        return $path;
    }

    /**
     * 拼接后的实际url
     * @param $uri string 内容oid uri
     * @return string
     */
    private function qrURL($uri) {
        $url = C('QR_URL') .'&redirect_uri='.C('QR_REDIRECT') .$uri . C('QR_SUFFIX');
        return $url;
    }

    private function _qrpath($oid) {
        // $dir = 'F:/subversion/PUSH_2.0/PushUI/resource/qrcode/'.$oid;
        $C = new ConfigureModel();
        $qrcodeDir = $C->getconfpathbyname('qrcode_path');
        if (!$qrcodeDir) {
            $qrcodeDir = $_SERVER['DOCUMENT_ROOT'] .'/'.__ROOT__.'resource/qrcode';
        }
        unset($C);
        $qrcodeDir = rtrim($qrcodeDir, '\\/').'/';
        $dir = $qrcodeDir.$oid;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir.'/qrcode.png';
    }

}