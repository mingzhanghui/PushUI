<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-27
 * Time: 16:23
 */
namespace User\Controller;
use Common\Common\Config;
use Think\Controller;

use Home\Common\Server;

class SyncController extends Controller {
    public function __construct() {
        parent::__construct();

        if (!isset($_SESSION)) {
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['customer']) {
                redirect( U('User/Login/index').'?refer=' . urlencode(U()) );
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['customer'] = 1;
        }
    }

    public function index() {
        $this->display();
    }

    /**
     * 发送用户同步请求
     */
    public function syncUser() {
        // 初始化用户同步状态
        $model = new \User\Model\UsesynstatusModel();
        $model->reset();
        unset($model);

        //组装发送帧
        $search=array();
        // duta
        $search[0] = 100;
        $search[1] = 117;
        $search[2] = 116;
        $search[3] = 97;
        // 1007(dec) => 0x03ef
        $search[4]=0x00;
        $search[5]=0x00;
        $search[6]=0x03;
        $search[7]=0xEF;
        // 32(dec) => 0x20
        $search[8]=0;
        $search[9]=0;
        $search[10]=0;
        $search[11]=0X20;

        for ($i = 12; $i < 44; $i++) {
            $search[$i] = 0;
        }

        //应该接收到的数据
        $result=array();
        // 'd','u','t','r'
        $result[0]=100;
        $result[1]=117;
        $result[2]=116;
        $result[3]=114;
        // 2007
        $result[4]=0;
        $result[5]=0;
        $result[6]=7;
        $result[7]=-41;

        $result[8]=0;
        $result[9]=0;
        $result[10]=0;
        $result[11]=4;

        $result[12]=0;
        $result[13]=0;
        $result[14]=0;
        $result[15]=1;

        $Configure = new \Home\Model\ConfigureModel();
        $host = $Configure->getconfstrbyname('local_bind');
        $port = $Configure->getconfintbyname('port');

        //receive为接受数据的数组
        $receive=array();
        Server::sendSocketMsg($host, $port,$search,$receive,16);

        // 接受数据是否正确
        $diff = array_diff($receive, $result);

        $a = array_slice($receive, 12, 4);
        $data = Server::BytesArrChangeToInt($a);

        if (count($diff) > 0) {
            $code = -1;
            $msg = '同步失败';
        } else {
            $code = 0;
            $msg = '同步成功';
        }

        $this->ajaxReturn(
            array('code'=>$code, 'data'=>$data, 'msg'=>$msg)
        );
    }

    /**
     * 查询最新用户同步状态
     */
    public function syncStatus() {
        $fileds = array('id', 'status');
        $Usesync = new \User\Model\UsesynstatusModel();
        $t = $Usesync->field($fileds)->order('id desc')->limit(0,1)->select();
        $res = $t[0];

        $status = $res['status'];
        $msg = '';
        if ($status == '0') {
            $msg = '未同步';
        } else if ($status == '1') {
            $msg = '同步中';
        } else if ($status == '2') {
            $msg = '同步完成';
        }
        $res['msg'] = $msg;

        $this->ajaxReturn( $res );
    }

}