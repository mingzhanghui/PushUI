<?php
namespace Pushctrl\Controller;
use Think\Controller;
use Common\Common\Config;

class IndexController extends Controller {
    private $model = null;

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION)) {
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['pushcontrol']) {
                redirect( U('Pushctrl/Login/index').'?refer=' . urlencode(U()) );
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['pushcontrol'] = 1;
        }

        $this->model = new \Pushctrl\Model\IndexModel();
    }

    public function index() {
		$this->display();
	}

    public function listPush() {
        $ps = new \Pushctrl\Model\PushstatusModel();
        $res = $ps->listPush();
        $this->ajaxReturn( $res );
    }

    /**
     * 片名 简介 海报
     */
	public function getMediaInfo() {
        if (!isset($_GET['oid'])) {
            throw_exception(__METHOD__ . ': oid is not set');
        }
        $oid = I('get.oid');
        $res = $this->model->getMediaInfo($oid);
        $this->ajaxReturn($res);
    }

    /**
     * 读取最新播发系统状态监控
     * ID	Int	主键，自增长
     * TotalRate	Int	0-10000 总的播发进度百分比（实际显示时除以100）
     * CpuRate	Int	0-100 ，当前的CPU 占用率
     * RamRate	Int	0-100，内存占用率
     * HDiskRate	Int	0-100，硬盘空间使用情况
     * BCNState	Int	广播网播发速率
     * IPState	Int	双向网传输速率
     * BBCounter	Int	补包请求的个数
     */
    public function getSystemState() {
        $fields = array('totalrate', 'cpurate', 'ramrate', 'hdiskrate');
        $rows = M('pushdisplay')->field($fields)->order('id desc')->limit(0,1)->select();
        $this->ajaxReturn( $rows[0] );
    }

    /**
     * 广播网络 + 双向网络, 最近10条
     */
    public function getNetState() {
        $rows = M('pushdisplay')->field(array('bcnstate','ipstate'))->order('id desc')->limit(0,10)->select();

        $bcn = $ip = array();
        /*
        foreach ($rows as $row) {
            array_push($bcn, $row['bcnstate']);
            array_push($ip, $row['ipstate']);
        }
        $this->ajaxReturn(array('bcn'=>array_reverse($bcn), 'ip'=>array_reverse($ip)));
        */
        foreach ($rows as $row) {
            array_unshift($bcn, $row['bcnstate']);
            array_unshift($ip, $row['ipstate']);
        }
        $this->ajaxReturn(array('bcn'=>$bcn, 'ip'=>$ip));
    }

    /**
     * 数据修复状态
     */
    public function getbbcounter() {
        $rows = M('pushdisplay')->field('bbcounter')->order('id desc')->limit(0,10)->select();
        $bb = array();
        foreach($rows as $row) {
            array_unshift($bb, $row['bbcounter']);
        }
        $this->ajaxReturn(array('bb'=>$bb));
    }

    public function test() {
        // $res = $this->model->getMediaInfo("734CCD42DC810E9AD0012B1FE6836D36");
    }
}
