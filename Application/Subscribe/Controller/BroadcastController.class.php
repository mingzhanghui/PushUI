<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-03
 * Time: 13:19
 */
namespace Subscribe\Controller;
use Think\Controller;

use Common\Common\Config;
use Common\Common\Logger;

class BroadcastController extends Controller {
    private $model = null;

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION)) {
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['subscribe']) {
                redirect( U('Subscribe/Login/index').'?refer=' . urlencode(U()) );
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['subscribe'] = 1;
        }

        $this->model = new \Subscribe\Model\BroadcastModel();
    }

    // 根据日期 查询播发内容列表
    public function index() {
        $date = date('Y-m-d', time());
        if (isset($_GET['date']) && $_GET['date'] != '') {
            $date = I('get.date');
        }
        $rows = $this->model->listBroadcast($date);
        $this->assign('bclist', $rows);
        $this->assign('date', $date);

        // date list
        $dateList = $moveLeft = $moveRight = '';
        $this->model->dateList($dateList, $moveLeft, $moveRight, $date);
        $this->assign('dateList', $dateList);
        $this->assign('moveLeft', $moveLeft);
        $this->assign('moveRight', $moveRight);

        $this->display();
    }

    // 播发统计列表
    public function stat() {
        $date = date('Y-m-d', time());
        if (isset($_GET['date']) && $_GET['date'] != '') {
            $date = I('get.date');
        }
        $this->assign('date', $date);

        $ul = $moveLeft = $moveRight = '';
        $this->model->dateList($ul, $moveLeft, $moveRight, $date);

        $this->assign('uldate', $ul);
        $this->assign('moveLeft', $moveLeft);
        $this->assign('moveRight', $moveRight);

        $this->display();
    }

    public function listDateStat() {
        $date = date('Y-m-d', time());
        if (isset($_GET['date']) && $_GET['date'] != '') {
            $date = I('get.date');
        }
        // 游标上的日期
        $matrix = $this->model->listDateStat($date);
        // dump($matrix);
        $this->ajaxReturn($matrix);
    }

    // 播发历史记录
    public function history() {
        $date = date('Y-m-d', time());
        $startdate = $enddate = $date;

        if (isset($_GET['startdate']) && $_GET['startdate'] != '') {
            $startdate = I('get.startdate');
        }
        if (isset($_GET['enddate']) && $_GET['enddate'] != '') {
            $enddate = I('get.enddate');
        }
        // 开始时间不能大于结束时间
        if (strtotime($startdate) > strtotime($enddate)) {
            $t = $startdate;
            $startdate = $enddate;
            $enddate = $t;
        }

        $list = $this->model->listHistoryByDate($startdate, $enddate);
        // dump($rows);
        $this->assign('list', $list);
        $this->assign('empty', '<tr class="dumb"><td colspan="7" class="text-center text-info">暂时没有数据</td></tr>');

        $this->assign('startdate', $startdate);
        $this->assign('enddate', $enddate);

        $this->display();
    }

    // 播发总量控制
    public function control() {
        $quota = $this->model->getQuota();
        $this->assign('quota', $quota);

        $this->display();
    }

    // 播发总量控制 修改总量
    public function setQuota() {
        if (!isset($_GET['intvalue'])) {
            throw_exception('cannot get intvalue!');
        }
        $intvalue = floatval(I('get.intvalue'));

        $where = array('name' => 'totalsource');
        $Configure = M("configure");
        $t = $Configure->where($where)->field('intvalue')->select();

        $code = '-1';
        $msg = 'failed';
        if ($intvalue > 0) {
            // add or edit
            if (is_null($t[0]['intvalue'])) {
                // modify totalsource
                $data = array('name' => 'totalsource', 'intvalue' => $intvalue);
                $code = $Configure->data($data)->add();
                $msg = 'add';
            } else {
                $data = array('intvalue' => $intvalue);
                $code = $Configure->where($where)->data($data)->save();
                $msg = 'update';
            }
            // log
            $config = Config::getInstance();
            if ($config->hasDailyRecord()) {
                Logger::setpath(C('DAILY_RECORD_DIR'));
                Logger::write('业务管理/播发日程/播发总量控制: '.json_encode($_GET), $_SESSION['username']);
            }
        }
        $this->ajaxReturn(array('code'=>$code, 'msg'=>$msg, 'value'=>$intvalue));
    }

}