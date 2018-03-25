<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-03
 * Time: 13:12
 */
namespace Subscribe\Controller;
use Subscribe\Model\MissionModel;
use Think\Controller;
use Common\Common\Config;
use Home\Common\Server;

class SyncController extends Controller {
    // public static $root;
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
        // self::$root = $_SERVER['DOCUMENT_ROOT'] . __ROOT__;
        $this->model = new \Subscribe\Model\SyncModel();
    }

    public function index() {
        // 内容同步
        // xhr: listContentSync, listMission

        // 查看处理日志
        $loglist = $this->getLogList();
        $this->assign('loglist', $loglist);

        $this->display();
    }

    // 内容同步 左边同步进度表格
    public function listContentSync() {
        $Mediasynlog = new \Subscribe\Model\MediasynlogModel();
        $result = $Mediasynlog->listContentSync();
        $this->ajaxReturn( $result );
    }

    // 内容同步 右边业务期表格
    public function listMission() {
        $result = $this->model->listMission();
        $this->ajaxReturn( $result );
    }

    /**
     * 同步业务信息: UI向UI-Server请求同步数据到播发服务器
     */
    public function sendSyncSocket() {
        //组装发送帧
        $sync = array();
        for ($i = 0; $i < 44; $i++) {
            $sync[$i] = 0;
        }
        // 'd' 'u' 't' 'a'
        $sync[0]=100;
        $sync[1]=117;
        $sync[2]=116;
        $sync[3]=97;
        // 1003
        $sync[4] = 0x00;
        $sync[5] = 0x00;
        $sync[6] = 0x03;
        $sync[7] = 0xeb;
        // 32
        $sync[8]=0;
        $sync[9]=0;
        $sync[10]=0;
        $sync[11]=0x20;

        // 应该接收到的 UI-Server服务器数据回复参数
        $result = array();
        // 'd' 'u' 't' 'r' -> 4Bytes
        $result[0]=100;
        $result[1]=117;
        $result[2]=116;
        $result[3]=114;
        // 2003 -> 4Bytes
        $result[4] = 0;
        $result[5] = 0;
        $result[6] = 7;
        $result[7] = -45;  // 0xd3
        // 0 -> 4Bytes
        for ($i = 8; $i < 12; $i++) {
            $result[$i] = 0;
        }

        $Configure = new \Home\Model\ConfigureModel();
        $ip = $Configure->getsocketip();
        $port = $Configure->getsocketport();

        //receive为接受数据的数组
        $receive = array();
        $ret = Server::sendSocketMsg($ip, $port, $sync, $receive, 12);
        if ($ret['code'] !== 0) {
            $this->ajaxReturn( $ret );
        }

        $diff = array_diff($receive, $result);
        if (0===count($diff)) {
            $code = 0;
            $data = null;
            $msg = 'send success';
        } else {
            $replypwd = array_slice($receive, 4, 4);
            $code = -1;
            $data = Server::BytesArrChangeToInt($replypwd);
            switch ($data) {
                case 0: $msg = '无频道信息下发'; break;
                case 1: $msg = '帧头格式非法'; break;
                case 2: $msg = '请求口令非法'; break;
                case 3: $msg = '认证码错误'; break;
                case 5: $msg = '其他系统错误'; break;
                default: $msg = 'unknown error';
            }
        }
        $ret['code'] = $code;
        $ret['data'] = $data;
        $ret['msg'] = $msg;
        $this->ajaxReturn( $ret );
    }


    /**
     * 查询业务同步状态: 发送查询Socket帧
     * array
     * success: array['code'] == 0
     * syncing: array['code'] == 1
     * error:   array['code'] == 2
     */
    public function sendQuerySocket() {
        //组装发送帧
        $search = array();
        // 'duta'
        $search[0]=100;
        $search[1]=117;
        $search[2]=116;
        $search[3]=97;
        // 1004
        $search[4]=0x00;
        $search[5]=0x00;
        $search[6]=0x03;
        $search[7]=0xEC;
        // 32
        $search[8]=0;
        $search[9]=0;
        $search[10]=0;
        $search[11]=0x20;
        // 请求数据	ASCII	32Bytes	认证码，测试时可以为全0
        for ($i = 12; $i < 44; $i++) {
            $search[$i] = 0;
        }

        //应该接收到的数据
        $result = array();
        // 'dutr'
        $result[0]=100;
        $result[1]=117;
        $result[2]=116;
        $result[3]=114;
        // 2004  0x07d4 该值=2004时，有后续字段，否则无后续字段
        $result[4] = 0;
        $result[5] = 0;
        $result[6] = 0x07;
        $result[7] = -44;   // 0xd4
        // 回复数据长度4
        $result[8] = 0;
        $result[9] = 0;
        $result[10] = 0;
        $result[11] = 4;
        // 同步状态 0-同步已结束, 1-正在同步中, 2-同步出错
//        $result[12]=0;
//        $result[13]=0;
//        $result[14]=0;
//        $result[15]=1;

        // ipaddr + port
        $Configure = new \Home\Model\ConfigureModel();
        $ip = $Configure->getsocketip();
        $port = $Configure->getsocketport();

        $receive=array();
        define('BUFSIZE', 16);
        Server::sendSocketMsg($ip, $port, $search, $receive, BUFSIZE);
        $diff = array_diff(array_slice($receive, 0, 12), $result);

        if (0===count($diff)) {
            $a = array_slice($receive, 12, 4);
            $code = Server::BytesArrChangeToInt($a);
            switch ($code) {
                case 0:
                    // 同步完了 清空pushstatus
                    /*
                    $ps = new PushstatusModel();
                    $ps->where(true)->delete();
                    M()->execute('vacuum');
                    unset($ps);
                    */
                    $msg = 'sync done';
                    break;
                case 1: $msg = 'sync ongoing'; break;
                case 2: $msg = 'sync error'; break;
                default: $msg = 'unknown sync status';
            }
        } else {
            $code = -1;
            $msg = 'reply format error';
        }
        $ret = array('code'=>$code, 'msg'=>$msg, 'data'=>$receive);
        $this->ajaxReturn( $ret );
    }

    // 查看日志内容
    public function getLogContent($name) {
        $path = $this->model->getLogDir() .'/'. $name;
        $content = nl2br(file_get_contents($path));
        $this->ajaxReturn($content);
    }

    private function getLogList() {
        // $logdir = self::$root . '/resource/logcontent';
        $logdir = $this->model->getLogDir();

        $handle = opendir($logdir);
        if (!$handle) {
            throw_exception("failed to open directory " . $logdir);
        }
        $files = array();
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..') {
                array_push($files, $entry);
            }
        }
        closedir($handle);

        rsort($files, SORT_STRING);

        return $files;
    }

    /**
     * 同步完了 设置上次同步状态
     * @param $name
     */
    public function setLastSyncState() {
        $a =  json_decode( $_POST['missions'] );
        $state = $_POST['state'];

        $mission = new MissionModel();
        // $where['id'] = array('in', $a);
        // return $mission->where($where)->data(array('state' => $state))->save();
        $where = array('id' => 0);
        $data = array('state' => $state);
        $res = array();   // 返回修改完了的mission id
        foreach ($a as $id) {
            $where['id'] = $id;
            $n = $mission->where($where)->data($data)->save();
            // echo $mission->getLastSql() . '<br />';
            if ($n > 0) {
                $res[] = $id;
            }
        }

        $this->ajaxReturn( $res );
    }

    /**
     * 取得需要同步的mission id(VersionID != SynVersionID)
     */
    public function missionsToSync() {
        $where = 'versionid != synversionid';
        $mission = new MissionModel();
        $rows = $mission->where($where)->field('id')->select();
        $a = array();
        foreach ($rows as $row) {
            $a[] = $row['id'];
        }
        $this->ajaxReturn($a);
    }

    public function test() {
        $a = array(128, 32, 129, 1);
        /*
$int = $this->model->BytesArrChangeToInt($a);
printf("%d.%d.%d.%d<br />", $int>>24, ($int & 0x00ff0000) >> 16, ($int & 0x0000ff00) >> 8, ($int & 0x000000ff));
echo '<br />';
$s = '¥duta';
dump( $this->model->getbytes($s) );
*/
        $b = array(128, 32, 129, 1);
        $result = array_diff($a, $b);
        dump(count($result) === 0);
        dump( array_slice($b, 0, 2) );
    }


}