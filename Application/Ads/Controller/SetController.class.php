<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-14
 * Time: 10:36
 */
namespace Ads\Controller;
use Think\Controller;

use Common\Common\Config;
use Common\Common\Logger;

class SetController extends Controller {

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
        $this->display();
    }

    public function setadpath() {
        $Config = M('configure');
        $res = array(
            'code' => 0,
            'msg'  => '更新成功',
            'data' => array('adlog_path'=>0,'adv_dir'=>0)
        );

        // 同步日志路径: D:/software/wamp/www/PushUI/resource/logadv
        $adlog_path = I('post.Adlog_path');
        if ('' === $adlog_path) {
            $res['code']= 1;
            $res['msg'] = '同步日志路径不能为空';
            $this->ajaxReturn( $res );
        }
        $where = array('name' => 'adlog_path');
        $data = array('stringvalue' => $adlog_path);
        $a = $Config->where($where)->field('stringvalue')->select();
        if (is_null($a[0]['stringvalue'])) {
            $data = array_merge($data, $where);
            $rlog = $Config->data($data)->add();
        } else {
            $rlog = $Config->where($where)->data($data)->save();
        }

        // 广告存储路径: D:/software/wamp/www/PushUI/resource/adv
        $adv_dir = I('post.Adv_path');
        if ('' === $adlog_path) {
            $res['code']= 1;
            $res['msg'] = '广告存储路径不能为空';
            $this->ajaxReturn( $res );
        }

        $where = array('name' => 'adv_dir');
        $data = array('stringvalue' => $adv_dir);
        $a = $Config->where($where)->field('stringvalue')->select();
        if (is_null($a[0]['stringvalue'])) {
            $data = array_merge($data, $where);
            $r = $Config->data($data)->add();
        } else {
            $r = $Config->where($where)->data($data)->save();
        }
        if ($rlog < 1 || $r < 1) {
            $res['code'] = 2;
            $res['msg'] = '路径更改失败';
        }
        $res['data']['adlog_path'] = $rlog;
        $res['data']['adv_dir'] = $r;

        // log
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('广告管理/系统设置: '.json_encode($_POST), $_SESSION['username']);
        }

        $this->ajaxReturn($res);
    }

    public function getlogadvpath() {
        $config = new \Home\Model\ConfigureModel();

        $this->ajaxReturn( $config->getconfigbyname('adlog_path') );
    }

    public function getadvpath() {
        $config = new \Home\Model\ConfigureModel();

        $this->ajaxReturn( $config->getconfigbyname('adv_dir') );
    }

    public function checkPath($path) {
        $print_danger = function ($msg) {
            printf('<font color="red">%s</font>', $msg);
        };
        if (!file_exists($path)) {
            $print_danger($path.'不存在');
            return false;
        }
        if (!is_dir($path)) {
            $print_danger($path . '不是有效的目录</font>');
            return false;
        }
        if (!is_writable($path)) {
            $print_danger($path . '目录不可写');
            return false;
        }
        return true;
    }

}