<?php
namespace Ads\Controller;
use Think\Controller;

use Common\Common\Config;
use Common\Common\Logger;

/**
 * 广告编辑
 * Class IndexController
 * @package Ads\Controller
 */
class IndexController extends Controller {
    private $model = null;

    public function __construct() {
        parent::__construct();

        if (!isset($_SESSION)) {
            session_start();
        }

        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['advertise']) {
                redirect( U('Ads/Login/index').'?refer=' . urlencode(U()) );
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['advertise'] = 1;
        }

        $this->model = new \Ads\Model\IndexModel();
    }

    /**
     * 片头广告列表
     * 利用 \Think\Page 分页
     */
    public function index() {
        $get = array_filter( I('get.') );

        // 查询条件 key value pair
        $search = array();
        foreach ($get as $key => $value) {
            if ($key === 'name' || $key === 'advertisename') {
                $search[$key] = $value;
            }
        }
        unset($get);

        $map = array();
        foreach($search as $key => $value) {
            $map[$key] = array('like', '%'.$value.'%');
        }
        $Preroll = M('AdPrerolladmedia');

        $count = $Preroll->where($map)->count();
        // #pagination: pagesize=10, 每页显示10条记录
        $Page = new \Think\Page($count, 10);
        // 页面显示5个跳转a链接
        $Page->rollPage = 3;
        //  分页显示输出
        $show = $Page->show();
        $this->assign('page', $show);

        $fields = array('id','contentid', 'advertisename', 'name', 'filetypeid', 'time', 'addeddate', 'mediastatus');
        $rows = $Preroll->where($map)
            ->field($fields)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('id desc')
            ->select();

        // 广告类型
        $adtypes = $this->model->mapAdFileType();
        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            $rows[$i]['filetype'] = $adtypes[ $rows[$i]['filetypeid'] ];
            unset($rows[$i]['filetypeid']);
        }
        $this->assign('adlist', $rows);

        // 文件类型
        $filetypes = $this->model->listFileTypes();
        $this->assign('filetypes', $filetypes);

        $this->display();
    }

    /**
     * 暂停广告
     */
    public function pause() {
        $get = array_filter( I('get.') );

        // 查询条件 key value pair
        $search = array();
        foreach ($get as $key => $value) {
            if ($key === 'name' || $key === 'advertisename') {
                $search[$key] = $value;
            }
        }
        unset($get);

        $map = array();
        foreach($search as $key => $value) {
            $map[$key] = array('like', '%'.$value.'%');
        }
        $Pause = M('AdPauseadmedia');

        $count = $Pause->where($map)->count();
        // #pagination: pagesize=10, 每页显示10条记录
        $Page = new \Think\Page($count, 10);
        // 页面显示5个跳转a链接
        $Page->rollPage = 3;
        //  分页显示输出
        $show = $Page->show();
        $this->assign('page', $show);

        $fields = array('id','contentid', 'advertisename', 'name', 'filetypeid', 'time', 'addeddate', 'mediastatus');
        $rows = $Pause->where($map)
            ->field($fields)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('id desc')
            ->select();

        // 广告类型
        $adtypes = $this->model->mapAdFileType();
        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            $rows[$i]['filetype'] = $adtypes[ $rows[$i]['filetypeid'] ];
            unset($rows[$i]['filetypeid']);
        }
        $this->assign('adlist', $rows);

        // 文件类型
        $filetypes = $this->model->listFileTypes();
        $this->assign('filetypes', $filetypes);

        $this->display();
    }

    /**
     * 添加片头广告
     */
    public function preRollAddMedia() {
        $data = I('post.');

        if (!isset($_FILES) || is_null($_FILES['file'])) {
            $this->error('文件太大，上传失败!', U('Ads/Index/index?e=1'), 3);
        }
        // 文件名
        $filename = $_FILES['file']['name'];
        // 广告名
        if (trim($data['advertisename']) === '') {
            $advname = explode('.', $filename);
            $data['advertisename'] = $advname[0];
        }

        $Preroll = M('AdPrerolladmedia');
        $where = array('name' => $filename);
        $t = $Preroll->where($where)->limit(0,1)->field('name')->select();
        if (!is_null($t[0]['name'])) {
            $this->error('已存在同名的文件, 请重新上传!', U('Ads/Index/index?e=2'), 3);
        }
        // 文件OID
        $data['contentid'] = strtoupper( md5_file($_FILES['file']['tmp_name']) );
        $data['size'] = $_FILES['file']['size'];

        $dir = $this->model->getAdvDir();

        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 100 * 1024 * 1024;   // 100M
        // $upload->exts = array('jpg', 'gif', 'png', 'jpeg', 'ts', 'mp4', 'wmv');  // 设置附件类型
        $upload->exts = $this->model->uploadPrefix();
        $upload->rootPath = './';   // 上传文件的根目录(相对于项目根目录)
        $upload->savePath = $dir;   // 设置附件上传目录(相对于$upload->rootPath)
        $upload->autoSub = false;   // 自动使用子目录保存上传文件 默认为true
        $upload->hash = false;    // 是否生成文件的hash编码 默认为true
        $upload->saveName = '';   // 上传文件名保持不变
        $upload->replace = true;  // replace 存在同名文件是否是覆盖，默认为false

        // 上传文件
        $info = $upload->uploadOne($_FILES['file']);
        if (!$info) {  // 上传错误提示信息
            $this->error($upload->getError(), U('Ads/Index/index?e=3'), 5);
        }

        // 上传文件的绝对路径
        $path = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . ltrim($info['savepath'].$info['savename'], '.');
        $data['url'] = $path;
        $data['name'] = $filename;    // to db <= 广告文件名

        $data['addeddate'] = date('Y-m-d', time());
        if (!isset($data['mediastatus'])) {
            $data['mediastatus'] = 0;
        }

        // dump($data);
        // contentid存在?
        $where = array('contentid' => $data['contentid']);
        $t = $Preroll->where($where)->limit(0,1)->field('contentid')->select();

        // 写入数据库
        $r = 0;
        if (is_null($t[0]['contentid'])) {
            $r = $Preroll->data($data)->add();
        } else {
            $r = $Preroll->data($data)->where($where)->save();
        }
        if ($r === 0) {
            $this->error('写入数据库失败', U('Ads/Index/index?e=4'), 3);
        }
        // log
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('广告管理/广告编辑/添加片头广告: '.json_encode($data), $_SESSION['username']);
        }

        // success => redirect
        $this->success('上传成功: '.$path, U('Ads/Index/index'), 3);
    }

    /**
     * 添加暂停广告
     */
    public function pauseAddMedia() {
        // dump($_POST); dump($_FILES); die;
        $data = I('post.');

        if (!isset($_FILES) || is_null($_FILES['file'])) {
            $this->error('文件太大，上传失败!', U('Ads/Index/pause'), 3);
        }
        // 文件名
        $filename = $_FILES['file']['name'];
        // 广告名
        if (trim($data['advertisename']) === '') {
            $advname = explode('.', $filename);
            $data['advertisename'] = $advname[0];
        }

        $Pause = M('AdPauseadmedia');
        $where = array('name' => $filename);
        $t = $Pause->where($where)->limit(0,1)->field('name')->select();
        if (!is_null($t[0]['name'])) {
            $this->error('已存在同名的文件, 请重新上传!', U('Ads/Index/pause'), 3);
        }
        // 文件OID
        $data['contentid'] = strtoupper( md5_file($_FILES['file']['tmp_name']) );
        $data['size'] = $_FILES['file']['size'];

        $dir = $this->model->getAdvDir();

        $upload = new \Think\Upload();  // 实例化上传类
        $upload->maxSize = 100 * 1024 * 1024;   // 100M
        // $upload->exts = array('jpg', 'gif', 'png', 'jpeg', 'ts', 'mp4', 'wmv');  // 设置附件类型
        $upload->exts = $this->model->uploadPrefix();
        $upload->rootPath = './';   // 上传文件的根目录(相对于项目根目录)
        $upload->savePath = $dir;   // 设置附件上传目录(相对于$upload->rootPath)
        $upload->autoSub = false;   // 自动使用子目录保存上传文件 默认为true
        $upload->hash = false;    // 是否生成文件的hash编码 默认为true
        $upload->saveName = '';   // 上传文件名保持不变
        $upload->replace = true;  // replace 存在同名文件是否是覆盖，默认为false

        // 上传文件
        $info = $upload->uploadOne($_FILES['file']);
        if (!$info) {  // 上传错误提示信息
            $this->error($upload->getError(), U('Ads/Index/pause'), 5);
        }

        // 上传文件的绝对路径
        $path = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . ltrim($info['savepath'].$info['savename'], '.');
        $data['url'] = $path;
        $data['name'] = $filename;    // to db <= 广告文件名

        $data['addeddate'] = date('Y-m-d', time());
        if (!isset($data['mediastatus'])) {
            $data['mediastatus'] = 0;
        }

        // contentid存在? : 'save' : 'add'
        $where = array('contentid' => $data['contentid']);
        $t = $Pause->where($where)->limit(0,1)->field('contentid')->select();

        // 写入数据库
        if (is_null($t[0]['contentid'])) {
            $r = $Pause->data($data)->add();
        } else {
            $r = $Pause->data($data)->where($where)->save();
        }
        if ($r === 0) {
            $this->error('写入数据库失败', U('Ads/Index/pause'), 3);
        }
        // log
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('广告管理/广告编辑/添加暂停广告: '.json_encode($data), $_SESSION['username']);
        }
        // success => redirect
        $this->success('上传成功: '.$path, U('Ads/Index/pause'), 3);
    }

    /**
     * 投放广告 => 业务类型
     * 列出所有叶子节点的业务包ID & 业务包名称
     */
    public function loadLeafPackage() {
        $fields = array('id', 'packagename');
        $rows = M('package')->where('isnode != 0')->field($fields)->order('id desc')->select();
        $this->ajaxReturn( $rows );
    }

    /**
     * 投放 片头/暂停 广告 AdPrerolladpushstatus
     */
    public function putAd() {
        $code = 0;
        $msg = 'success';
        $res = null;

        $data = I('post.');

        if (!isset($data['table'])) {
            $tableMedia = 'Ad_PreRollAdMedia';   // 默认是片头广告
            $tablePush = 'Ad_PreRollAdPushStatus';
        } else {
            $tableMedia = $data['table'];
            $tablePush = preg_replace('/Media$/', 'PushStatus', $tableMedia);
            unset($data['table']);
        }

        if (is_null($data['adid']) || $data['adid'] === '') {
            throw_exception(__METHOD__ . ': adid is not set!');
        }

        // BEGIN check date code=1,2
        if (!$this->model->mycheckdate($data['startdate'])) {
            $code = 1;
            $msg = '开始日期格式不是yyyy-mm-dd';
            $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
        }
        if (!$this->model->mycheckdate($data['enddate'])) {
            $code = 2;
            $msg = '结束日期格式不是yyyy-mm-dd';
            $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
        }

        $start = strtotime($data['startdate']);
        $end = strtotime($data['enddate']);

        // $start <= $end
        if ($start > $end) {
            $code = 1;
            $msg = '开始日期不能大于结束日期';
            $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
        }
        $now = strtotime( date('Y-m-d', time()) );
        if ($start < $now) {
            $code = 1;
            $msg = '开始日期不能小于今天';
            $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
        }
        // 3 - 已备播，表示当日未到该广告的投放开始日期
        if ($now < $start) {
            $data['status'] = 3;
        }
        // 1 - 投放中，表示当日包含在该广告的投放开始日期和截止日期之间（包括当天)
        else if ($now <= $end) {
            $data['status'] = 1;
        }
        // 2 - 已投放，表示当日大于该广告的投放截止日期
        else {
            // $data['status'] = 2;
            $code = 2;
            $msg = '投放截止日期不能小于今天';
            $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
        }
        // TODO: 检查日期范围 重叠
        // END checkdate

        // 字段过滤
        $fields = array('adid', 'packageid', 'status', 'startdate', 'enddate');
        $dataPush = array();
        foreach ($data as $key => $value) {
            if (in_array($key, $fields)) {
                $dataPush[$key] = $value;
            }
        }

        // Ad_PreRollAdPushStatus => AdPrerolladmedia (@file: /PushUI/Application/Ads/Common/function.php)
        $modelname = nametabletomodel($tablePush);
        $Push = M($modelname);

        $where = array('adid' => $data['adid'], 'packageid' => $data['packageid']);
        $c = $Push->where($where)->count();
        if ($c > 0) {
            unset($dataPush['adid']);
            unset($dataPush['packageid']);
            $res['push'] = $Push->data($dataPush)->where($where)->save();
        } else {
            $res['push'] = $Push->data($dataPush)->add();
        }
        if ($res['push'] < 1) {
            $code = 255;
            $msg = $Push->getLastSql();
            $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
        }

        //  MBIS_Server_Ad_PreRollAdMedia mediastatus += 1
        $adid = intval( $data['adid'] );
        // $sql = sprintf("UPDATE %sAd_PreRollAdMedia SET MediaStatus=MediaStatus+1 WHERE id=%d", C('DB_PREFIX'), $adid);
        $sql = sprintf("UPDATE %s%s SET MediaStatus=MediaStatus+1 WHERE id=%d", C('DB_PREFIX'), $tableMedia, $adid);
        $res['media'] = M()->execute($sql);

        // return code...
        $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
    }

    public function getPrerollAdMedia() {
        $id = I('get.id');
        if (is_null($id) || ''===$id) {
            throw_exception(__METHOD__ . ': ad id is not set');
        }
        $where = array('id' => $id);
        $fields = array('advertisename', 'name', 'url', 'filetypeid', 'time', 'description', 'addeddate');
        $t = M('AdPrerolladmedia')->where($where)->field($fields)->select();
        $this->ajaxReturn( $t[0] );
    }

    /**
     * show暂停广告详细信息 xhr
     */
    public function getPauseAdMedia() {
        $id = I('get.id');
        if (is_null($id) || ''===$id) {
            throw_exception(__METHOD__ . ': ad id is not set');
        }
        $where = array('id' => $id);
        $fields = array('advertisename', 'name', 'url', 'filetypeid', 'time', 'description', 'addeddate');
        $t = M('AdPauseadmedia')->where($where)->field($fields)->select();
        $this->ajaxReturn( $t[0] );
    }

    /**
     * 更新片头广告
     */
    public function putPrerollAdMedia() {
        $id = I('post.id');
        $refer = $_SERVER['HTTP_REFERER'];

        if (is_null($id) || ''===$id) {
            $this->error('没有选择片头广告文件', $refer, 3);
        }
        $data = I('post.');
        $where = array('id'=>$id);
        unset($data['id']);
        $ra = M('AdPrerolladmedia')->where($where)->data($data)->save();
        if ($ra > 0) {
            $this->success('片头广告更新成功!', $refer);
            // setcookie('adid', $id, time()+50);  // 50s
        } else {
            $this->error('片头广告更新失败', $refer, 3);
        }
    }

    /**
     * 更新暂停广告 form post
     */
    public function putPauseAdMedia() {
        $id = I('post.id');
        $refer = $_SERVER['HTTP_REFERER'];

        if (is_null($id) || ''===$id) {
            $this->error('没有选择暂停广告文件ID', $refer, 3);
        }
        $data = I('post.');
        $where = array('id'=>$id);
        unset($data['id']);
        $ra = M('AdPauseadmedia')->where($where)->data($data)->save();
        if ($ra > 0) {
            $this->success('暂停广告更新成功!', $refer);
            // setcookie('adid', $id, time()+50);  // 50s
        } else {
            $this->error('暂停广告更新失败', $refer, 3);
        }
    }

    /**
     * 检查片头广告是否有投放
     */
    public function hasPrerollAdPushStatus() {
        $s = I('post.s');
        if (is_null($s) || $s === '') {
            $this->error(__METHOD__ . ': id string is not set!', $_SERVER['HTTP_REFERER'], 3);
        }
        $a = explode(',', $s);
        $result = array('hasPush'=>false,'adid'=>'','name'=>'', 'count'=>0);

        $PS = M('AdPrerolladpushstatus');

        foreach($a as $adid) {
            $where = array('adid' => $adid);
            $n = $PS->where($where)->count();
            if ($n > 0) {
                $t = M('AdPrerolladmedia')->where(array('id'=>$adid))->field('name')->limit(0,1)->select();
                $name = $t[0]['name'];
                $result = array(
                    'hasPush' => true,
                    'adid'    => $adid,
                    'name'    => $name,
                    'count'   => $n
                );
                $this->ajaxReturn($result);
            }
        }
        $this->ajaxReturn($result);
    }

    /**
     * 检查暂停广告是否有投放
     */
    public function hasPauseAdPushStatus() {
        $result = array('hasPush'=>false,'adid'=>'','name'=>'', 'count'=>0);

        $s = I('post.s');
        if (is_null($s) || $s === '') {
            $this->error(__METHOD__ . ': id string is not set!', $_SERVER['HTTP_REFERER'], 3);
        }
        $a = explode(',', $s);
        $PS = M('AdPauseadpushstatus');

        foreach($a as $adid) {
            $where = array('adid' => $adid);
            $n = $PS->where($where)->count();
            if ($n > 0) {
                $t = M('AdPauseadmedia')->where(array('id'=>$adid))->field('name')->limit(0,1)->select();
                $name = $t[0]['name'];
                $result = array(
                    'hasPush' => true,
                    'adid'    => $adid,
                    'name'    => $name,
                    'count'   => $n
                );
                $this->ajaxReturn($result);
            }
        }
        $this->ajaxReturn($result);
    }

    /**
     * 删除片头广告
     */
    public function delPrerollAd() {
        $s = I('post.s');
        if (is_null($s) || $s === '') {
            $this->error(__METHOD__ . ': id string is not set!', $_SERVER['HTTP_REFERER'], 3);
        }
        $a = explode(',', $s);
        $Media = M('AdPrerolladmedia');
        $Pause = M('AdPauseadmedia');

        $sum = 0;   // 删除条目计数
        $redirect = U('Ads/Index/index');
        foreach($a as $id) {
            $where = array('id'=>$id);
            $fields = array('url', 'advertisename');
            $t = $Media->where($where)->field($fields)->select();
            $path = $t[0]['url'];
            $adname = $t[0]['advertisename'];

            // 片头广告 暂停广告共用一个目录 检查暂停广告中这个文件是否被使用
            $c = $Pause->where(array('url'=>$path))->count();
            if ($c === '0') {
                $d = unlink($path);
                $d || $this->error('删除文件: ' .$path.'失败!', $redirect, 3);
            }

            $n = $Media->where($where)->delete();
            $n > 0 || $this->error('删除片头广告' .$adname .'失败!', $redirect, 3);

            $sum += $n;
        }
        // info, status, url
        $this->success('删除'.$sum.'个片头广告!', $redirect, 3);
    }

    /**
     * 删除暂停广告
     */
    public function delPauseAd() {
        $s = I('post.s');
        if (is_null($s) || $s === '') {
            $this->error(__METHOD__ . ': id string is not set!', $_SERVER['HTTP_REFERER'], 3);
        }
        $a = explode(',', $s);
        $Pause = M('AdPauseadmedia');
        $Preroll = M('AdPrerolladmedia');

        $sum = 0;   // 删除条目计数
        $redirect = $_SERVER['HTTP_REFERER']; // U('Ads/Index/pause');

        foreach($a as $id) {
            $where = array('id'=>$id);
            $fields = array('url', 'advertisename');
            $t = $Pause->where($where)->field($fields)->select();
            $path = $t[0]['url'];
            $adname = $t[0]['advertisename'];

            // 片头广告 暂停广告共用一个目录 检查片头广告中这个文件是否被使用
            $c = $Preroll->where(array('url'=>$path))->count();
            if ($c === '0') {
                $d = unlink($path);
                $d || $this->error('删除文件: ' .$path.'失败!', $redirect, 3);
            }

            $n = $Pause->where($where)->delete();
            $n > 0 || $this->error('删除暂停广告' .$adname .'失败!', $redirect, 3);

            $sum += $n;
        }
        // info, status, url
        $this->success('删除'.$sum.'个暂停广告!', $redirect, 3);
    }

    public function test() {
        $table = 'Ad_PreRollAdMedia';
        $parts = explode('_', $table);
        $parts = array_map(function($part) {
            $part = strtolower($part);
            return ucwords($part);
        }, $parts);
        echo implode('', $parts);
    }

}