<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-14
 * Time: 10:34
 */
namespace Ads\Controller;
use Think\Controller;

use Common\Common\Config;

/**
 * 广告投放
 * Class PutController
 * @package Ads\Controller
 */
class PutController extends Controller {
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
    }

    /**
     * 片头广告投放 列表
     */
    public function index() {
        // 默认显示分页 页数1
        if (!isset($_GET['p']) || $_GET['p']<1) {
            $_GET['p'] = 1;
        }

        $get = array_filter( I('get.') );

        // 查询条件 key value pair
        $search = array();
        foreach ($get as $key => $value) {
            // filter 查看方式 & 选择广告日期
            if ($key === 'status' || $key === 'date') {
                $search[$key] = $value;
            }
        }
        unset($get);

        $map = array();   // SQL查询AdPrerolladpushstatus条件
        if (isset($search['status'])) {
            if (($status = $search['status']) != 0) {
                $map['status'] = array('eq', $status);
            }
        }
        if (isset($search['date'])) {
            $date = $search['date'];
            $map['startdate'] = array('elt', $date);
            $map['enddate'] = array('egt', $date);
        }
        unset($search);

        $Push = M('AdPrerolladpushstatus');
        {
            $count = $Push->where($map)->count();
            // #pagination: pagesize=10, 每页显示10条记录
            $pagesize = 10;
            $Page = new \Think\Page($count, $pagesize);   // pagesize=2, for pagination test
            // 页面显示5个跳转a链接
            $Page->rollPage = 3;
            //  分页显示输出
            $show = $Page->show();
            $this->assign('page', $show);

            $p = I('get.p');
            $offset = ($p-1) * $pagesize;
            $this->assign('offset', $offset);   // 起始序号偏移量
        }

        // 投放状态记录ID, 投放状态, from: 投放开始时间  to: 投放结束时间,  入库时间,
        $fields = array('id','adid', 'packageid', 'status', 'startdate', 'enddate');
        $rows = $Push->where($map)
            ->field($fields)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('id desc')
            ->select();

        // 广告文件类型
        $model = new \Ads\Model\IndexModel();
        $adtypes = $model->mapAdFileType();

        $Media = M('AdPrerolladmedia');
        // 广告名称, 文件名称, 文件类型, 时长
        $fields = array('advertisename', 'name', 'filetypeid', 'time', 'addeddate');
        // 业务包
        $Package = M('package');

        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            $where = array('id' => $rows[$i]['adid']);
            $t = $Media->field($fields)->where($where)->select();
            $rows[$i] = array_merge($rows[$i], $t[0]);
            $rows[$i]['filetype'] = $adtypes[ $rows[$i]['filetypeid'] ];
            unset($rows[$i]['filetypeid']);

            $t = $Package->where(array('id' => $rows[$i]['packageid']))->field('packagename')->select();
            $rows[$i]['packagename'] = $t[0]['packagename'];

            // 状态
            $rows[$i]['status'] = $this->getAdsStatus($rows[$i]['status']);
        }
        $this->assign('list', $rows);

        $empty = '<tr><td colspan="11">暂时没有数据</td></tr>';
        $this->assign('empty', $empty);

        $this->display();
    }

    /**
     * 暂停广告投放列表
     */
    public function pause() {
        // 默认显示分页 页数1
        if (!isset($_GET['p']) || $_GET['p']<1) {
            $_GET['p'] = 1;
        }
        $get = array_filter( I('get.') );

        // 查询条件 key value pair
        $search = array();
        foreach ($get as $key => $value) {
            // filter 查看方式 & 选择广告日期
            if ($key === 'status' || $key === 'date') {
                $search[$key] = $value;
            }
        }
        unset($get);

        $map = array();   // SQL查询AdPrerolladpushstatus条件
        if (isset($search['status'])) {
            if (($status = $search['status']) != 0) {
                $map['status'] = array('eq', $status);
            }
        }
        if (isset($search['date'])) {
            $date = $search['date'];
            $map['startdate'] = array('elt', $date);
            $map['enddate'] = array('egt', $date);
        }
        unset($search);

        $Push = M('AdPauseadpushstatus');
        {
            $count = $Push->where($map)->count();
            // #pagination: pagesize=10, 每页显示10条记录
            $pagesize = 10;
            $Page = new \Think\Page($count, $pagesize);   // pagesize=2, for pagination test
            // 页面显示5个跳转a链接
            $Page->rollPage = 3;
            //  分页显示输出
            $show = $Page->show();
            $this->assign('page', $show);

            $p = I('get.p');
            $offset = ($p-1) * $pagesize;
            $this->assign('offset', $offset);   // 起始序号偏移量
        }

        // 投放状态记录ID, 投放状态, from: 投放开始时间  to: 投放结束时间,  入库时间,
        $fields = array('id','adid', 'packageid', 'status', 'startdate', 'enddate');
        $rows = $Push->where($map)
            ->field($fields)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('id desc')
            ->select();

        // 广告文件类型
        $model = new \Ads\Model\IndexModel();
        $adtypes = $model->mapAdFileType();

        $Media = M('AdPauseadmedia');
        // 广告名称, 文件名称, 文件类型, 时长
        $fields = array('advertisename', 'name', 'filetypeid', 'time', 'addeddate');
        // 业务包
        $Package = M('package');

        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            $where = array('id' => $rows[$i]['adid']);
            $t = $Media->field($fields)->where($where)->select();
            $rows[$i] = array_merge($rows[$i], $t[0]);
            $rows[$i]['filetype'] = $adtypes[ $rows[$i]['filetypeid'] ];
            unset($rows[$i]['filetypeid']);

            $t = $Package->where(array('id' => $rows[$i]['packageid']))->field('packagename')->select();
            $rows[$i]['packagename'] = $t[0]['packagename'];

            // 状态
            $rows[$i]['status'] = $this->getAdsStatus($rows[$i]['status']);
        }
        $this->assign('list', $rows);

        $empty = '<tr><td colspan="11">暂时没有数据</td></tr>';
        $this->assign('empty', $empty);

        $this->display();
    }

    /**
     * 广告投放 状态码 => 状态字符串
     */
    private function getAdsStatus($st) {
        switch($st) {
            case '1' : $s = '投放中'; break;
            case '2' : $s = '已过期'; break;
            case '3' : $s = '未投放'; break;
            default: $s = 'unknown'; break;
        }
        return $s;
    }

    /**
     * 片头广告投放编辑  xhr
     */
    public function editPut() {
        $res = null;

        $data = array_filter( I('post.') );
        if ($data['adid'] === '' || $data['table'] === '' || $data['adid'] === '') {
            throw_exception(__METHOD__ . ': id, adid or table are not set!');
        }

        // 准备修改广告名
        $adid = $data['adid'];
        $advertisename = $data['advertisename'];
        unset($data['adid'], $data['advertisename']);

//      $tableMedia = 'Ad_PreRollAdMedia';
//      $tablePush = 'Ad_PreRollAdPushStatus';
        $tableMedia = $data['table'];
        $tablePush = preg_replace('/Media$/', 'PushStatus', $tableMedia);
        unset($data['table']);

        // BEGIN check date code=1,2
        $model = new \Ads\Model\IndexModel();
        if (!$model->mycheckdate($data['startdate'])) {
            $code = 1;
            $msg = '开始日期格式不是yyyy-mm-dd';
            $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
        }
        if (!$model->mycheckdate($data['enddate'])) {
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
        // 今天的起始时间戳
        $now = strtotime( date('Y-m-d', time()) );
        // 1 - 投放中，表示当日包含在该广告的投放开始日期和截止日期之间（包括当天)
        // 2 - 已投放，表示当日大于该广告的投放截止日期
        // 3 - 已备播，表示当日未到该广告的投放开始日期
        if ($now < $start) {
            $data['status'] = 3;
        } else if ($now <= $end) {
            $data['status'] = 1;
        } else {
            // $data['status'] = 2;
            $code = 2;
            $msg = '投放截止日期不能小于今天';
            $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
        }
        // TODO: 检查日期范围 重叠
        // END checkdate

        // Ad_PreRollAdPushStatus => AdPrerolladmedia (@file: /PushUI/Application/Ads/Common/function.php)
        $modelpushname = nametabletomodel($tablePush);
        $Push = M($modelpushname);

        $where = array('id'=>$data['id']);
        $res['push'] = $Push->data($data)->where($where)->save();
        if ($res['push'] < 1) {
            $code = 255;
            $msg = $Push->getLastSql();
            $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'res'=>$res));
        }
        unset($data);

        // 修改广告名称
        $w = array('id' => $adid);
        $d = array('advertisename' => $advertisename);
        $modelmedianame = nametabletomodel($tableMedia);
        $res['media'] = M($modelmedianame)->where($w)->data($d)->save();

        $this->ajaxReturn(array('code'=>0, 'msg'=>'success', 'res'=>$res));
    }

    /**
     * 片头广告投放编辑 读取原来数据
     */
    public function getPrerollPut() {
        $id = I('get.id');
        if (!isset($id) || $id === '') {
            throw_exception(__METHOD__ . ': id is not set');
        }
        // 广告名称
        $fields = array('id','adid', 'packageid', 'status', 'startdate', 'enddate');
        $where = array('id' => $id);
        $Push = M('AdPrerolladpushstatus');
        $t = $Push->where($where)->field($fields)->limit(0,1)->select();
        $res = $t[0];
        unset($t);

        $res['status'] = $this->getAdsStatus($res['status']);
        $fields = array('advertisename', 'name');
        $t = M('AdPrerolladmedia')->where(array('id' => $res['adid']))->field($fields)->select();
        $res['advertisename'] = $t[0]['advertisename'];
        $res['name'] = $t[0]['name'];

        $this->ajaxReturn($res);
    }

    /**
     * 暂停广告投放编辑 读取原来数据
     */
    public function getPausePut() {
        $id = I('get.id');
        if (!isset($id) || $id === '') {
            throw_exception(__METHOD__ . ': id is not set');
        }
        // 广告名称
        $fields = array('id','adid', 'packageid', 'status', 'startdate', 'enddate');
        $where = array('id' => $id);
        $Push = M('AdPauseadpushstatus');
        $t = $Push->where($where)->field($fields)->limit(0,1)->select();
        $res = $t[0];
        unset($t);

        $res['status'] = $this->getAdsStatus($res['status']);
        $fields = array('advertisename', 'name');
        $t = M('AdPauseadmedia')->where(array('id' => $res['adid']))->field($fields)->select();
        $res['advertisename'] = $t[0]['advertisename'];
        $res['name'] = $t[0]['name'];

        $this->ajaxReturn($res);
    }

    public function cancelPush() {
        $data = I('post.');

        if ($data['ids'] === '' || $data['table'] === '') {
            throw_exception(__METHOD__ . ': cannot post ids & table');
        }
        $table = $data['table'];
        if ($table !== 'AdPrerolladpushstatus' && $table !== 'AdPauseadpushstatus') {
            throw_exception(__METHOD__ . ': unexpected table ' . $table);
        }
        $a = explode(',', $data['ids']);
        $map['id'] = array('in', $a);
        $ra = M($table)->where($map)->delete();
        if ($ra > 0) {
            M()->execute('vacuum');
            $this->success('删除'.$ra.'条广告投放');
        }
        $this->error('删除广告投放失败');
    }
}