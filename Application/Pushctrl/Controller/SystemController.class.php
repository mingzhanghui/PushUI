<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-13
 * Time: 13:27
 */
namespace Pushctrl\Controller;

use Think\Controller;

use Common\Common\Config;
use Common\Common\Logger;
use Pushctrl\Model\SystemModel;

class SystemController extends Controller {

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
    }

    public function index() {
        $Conf = M('configure');
        // 修改播发设置
        $ret = -1;  // 修改状态  1: success, 0: fail, -1: 不需要修改
        if (IS_POST) {
            $map = array(
                'send_ratio'=>'intvalue',
                'start_time'=>'stringvalue',
                'sync_port'=>'intvalue',
                'ip_addr'=>'stringvalue',
                'local_bind'=>'stringvalue',
                'uPid'=>'intvalue'
            );
            foreach ($map as $key => $value) {
                $where = array('name'=>$key);
                $Conf->where($where)->data(array($value => I('post.'.$key)))->save();
            }
            unset($map);
            // UI-SERVER 请求同步配置修改
            $model = new SystemModel();
            $ret = $model->SendTbSocket();

            // 配置修改log
            $config = Config::getInstance();
            if ($config->hasDailyRecord()) {
                Logger::setpath(C('DAILY_RECORD_DIR'));
                Logger::write('播发管控/系统设置: '.json_encode($_POST), $_SESSION['username']);
            }
        }
        $this->assign('ret', $ret);
        $res = array(
            'send_ratio'=>null,
            'start_time'=>null,
            'sync_port'=>null,
            'ip_addr'=>null,
            'local_bind'=>null,
            'uPid'=>null
        );
        $names = array_keys($res);
        $fields = array('intvalue', 'stringvalue');

        while($name = current($names)) {
            $where = array('name'=>$name);
            $t = $Conf->where($where)->field($fields)->limit(0,1)->select();
            if ( is_null($t[0]['intvalue']) ) {
                $res[$name] = $t[0]['stringvalue'];
            } else {
                $res[$name] = $t[0]['intvalue'];
            }
            $name = next($names);
        }
        $this->assign('conf', $res);

        $this->display();
    }

    public function test() {
        $model = new SystemModel();
        $model->SendTbSocket();
    }
}