<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-14
 * Time: 10:35
 */
namespace Ads\Controller;
use Think\Controller;
use Common\Common\Config;

/**
 * 广告管理-统计报表
 * Class ReportController
 * @package Ads\Controller
 */
class ReportController extends Controller {
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

    public function index() {
      // 默认显示分页 页数1
      if (!isset($_GET['p']) || $_GET['p']<1) {
          $_GET['p'] = 1;
      }

      $get = array_filter( I('get.') );

      // 查询条件 key value pair
      $map = array();
      foreach ($get as $key => $value) {
          // filter 查看方式 & 选择广告日期
          if ($key === 'startdate' || $key === 'enddate') {
              $map[$key] = array('eq', $value);
          }
      }
      unset($get);

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

      // 广告文件类型 video / picture
      $model = new \Ads\Model\IndexModel();
      $adtypes = $model->mapAdFileType();

      $Media = M('AdPrerolladmedia');
      // 广告名称, 文件名称, 文件类型, 时长
      $fields = array('advertisename', 'name', 'filetypeid', 'time');
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
      }
      $this->assign('list', $rows);

      $empty = '<tr><td colspan="11">暂时没有数据</td></tr>';
      $this->assign('empty', $empty);

      $this->display();
  }

    /**
     * 统计报表 - 暂停广告列表
     */
    public function pause() {
        // 默认显示分页 页数1
        if (!isset($_GET['p']) || $_GET['p']<1) {
            $_GET['p'] = 1;
        }

        $get = array_filter( I('get.') );

        // 查询条件 key value pair
        $map = array();
        foreach ($get as $key => $value) {
            // filter 查看方式 & 选择广告日期
            if ($key === 'startdate' || $key === 'enddate') {
                $map[$key] = array('eq', $value);
            }
        }
        unset($get);

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

        // 广告文件类型 video / picture
        $model = new \Ads\Model\IndexModel();
        $adtypes = $model->mapAdFileType();

        $Media = M('AdPauseadmedia');
        // 广告名称, 文件名称, 文件类型, 时长
        $fields = array('advertisename', 'name', 'filetypeid', 'time');
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
        }
        $this->assign('list', $rows);

        $empty = '<tr><td colspan="11">暂时没有数据</td></tr>';
        $this->assign('empty', $empty);

        $this->display();
    }
}