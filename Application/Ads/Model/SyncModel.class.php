<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-08
 * Time: 10:51
 */
namespace Ads\Model;
use Think\Model;

class SyncModel extends Model {
    // 列出所有需要同步的 片头/暂停广告 ID
    public function listpushidts($table = 'AdPrerolladpushstatus') {
        if ($table !== 'AdPrerolladpushstatus' && $table !== 'AdPauseadpushstatus') {
            E(__METHOD__.': UNEXPECTED TABLE!');
        }
        $Push = M($table);
        $today = date('Y-m-d', time());
        $map['enddate'] = array('egt', $today);
        $t = $Push->where($map)->field('id')->select();
        return array_column($t, 'id');
    }

    /**
     * 广告同步列表
     * @param string $table
     * @return mixed
     */
    public function listPush($table = 'preroll') {
        if ($table !== 'preroll' && $table !== 'pause') {
            E(__METHOD__.': UNEXPECTED TABLE!');
        }
        // 1	片头广告 2	挂角广告 3	暂停广告
        if ($table == 'preroll') {
            $adtypeid = 1;
            $adtype = '片头广告';
        } else if ($table == 'pause') {
            $adtypeid = 3;
            $adtype = '暂停广告';
        } else if ($table == 'rolead') {
            $adtypeid = 2;
            $adtype = '挂角广告';
        } else {
            $adtypeid = 0;
            $adtype = '未定义';
        }

        $table = ucfirst($table);
        $Push = M(
            sprintf('Ad%sadpushstatus', $table)
        );
        $today = date('Y-m-d', time());
        $map['enddate'] = array('egt', $today);
        $fields = array('adid', 'packageid', 'startdate', 'enddate');
        $rows = $Push->where($map)->field($fields)->select();
        $n = count($rows);

        $Media = M(
            sprintf('Ad%sadmedia', $table)
        );
        $fields = array('contentid', 'advertisename', 'name');
        $Log = M('AdLog');
        $Package = M('package');
        // packagename, 同步状态
        for ($i = 0; $i < $n; $i++) {
            $a = $Package->field('packagename')->where(array('id' => $rows[$i]['packageid']))->limit(0,1)->select();
            $rows[$i]['packagename']= $a[0]['packagename'];

            $where = array('id' => $rows[$i]['adid']);
            $a = $Media->where($where)->field($fields)->limit(0,1)->select();
            $rows[$i] = array_merge($rows[$i], $a[0]);
            unset($a);

            $where = array(
                'contentid' => $rows[$i]['contentid'],
                'adtypeid'  => $adtypeid
            );
            $b = $Log->field('status')->where($where)->limit(0,1)->select();
            $status = $b[0]['status'];
            if (is_null($status)) {
                $rows[$i]['status'] = '未同步';
            } else if ($status < 0) {
                $rows[$i]['status'] = '同步失败';
            } else if ($status == '100') {
                $rows[$i]['status'] = '同步完成';
            } else {
                $rows[$i]['status'] = $status . '%';
            }
            $rows[$i]['adtype'] = $adtype;
        }
        return $rows;
    }

    /**
     * 发送广告同步请求
     */
    public function SendSyncSocket() {
        //组装发送帧
        $search=array();
        $search[0] = 100;
        $search[1] = 117;
        $search[2] = 116;
        $search[3] = 97;
        $search[4]=0x00;
        $search[5]=0x00;
        $search[6]=0x03;
        $search[7]=0xED;
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
        // 2005
        $result[4]=0x00;
        $result[5]=0x00;
        $result[6]=0x07;
        $result[7]=0xd5;

        $result[8]=0;
        $result[9]=0;
        $result[10]=0;
        $result[11]=0;

        $host = $this->getsocketip();
        $port = $this->getsocketport();

        //receive为接受数据的数组
        $receive=array();
        $this->sendSocketMsg($host, $port,$search,$receive,12);

        //接受数据是否正确
        $diff = array_diff($receive, $result);
        if (0===count($diff)) {
            $code = 0;
            $data = null;
            $msg = 'send success';
        } else {
            $replypwd = array_slice($receive, 4, 4);
            $code = -1;
            $data = $this->BytesArrChangeToInt($replypwd);
            switch ($data) {
                case 0: $msg = '无频道信息下发'; break;
                case 1: $msg = '帧头格式非法'; break;
                case 2: $msg = '请求口令非法'; break;
                case 3: $msg = '认证码错误'; break;
                case 5: $msg = '其他系统错误'; break;
                default: $msg = 'unknown error';
            }
        }
        return array('code'=>$code, 'data'=>$data, 'msg'=>$msg);
    }

    /**
     * 查询广告同步状态
     * @return array
     */
    public function SendQuerySocket() {
        $search=array();
        $search[0]=100;
        $search[1]=117;
        $search[2]=116;
        $search[3]=97;
        $search[4]=0x00;
        $search[5]=0x00;
        $search[6]=0x03;
        $search[7]=0xEE;
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
        // 该值=2004时，有后续字段，否则无后续字段
        $result[4]=0;
        $result[5]=0;
        $result[6]=0x07;
        $result[7]=0xd4;
        // 回复数据长度 4
        $result[8]=0;
        $result[9]=0;
        $result[10]=0;
        $result[11]=4;
        // 同步状态 0-同步已结束, 1-正在同步中, 2-同步出错
//        $result[12]=0;
//        $result[13]=0;
//        $result[14]=0;
//        $result[15]=1;

        $ip = $this->getsocketip();
        $port = $this->getsocketport();

        // 接受数据是否正确
        $receive=array();
        define('BUFSIZE', 16);
        $this->sendSocketMsg($ip, $port, $search, $receive, BUFSIZE);
        $diff = array_diff(array_slice($receive, 0, 12), $result);

        if (0===count($diff)) {
            $a = array_slice($receive, 12, 4);   // 12-15
            $code = $this->BytesArrChangeToInt($a);
            switch ($code) {
                case 0: $msg = '同步已结束'; break;
                case 1: $msg = '正在同步中'; break;
                case 2: $msg = '同步出错'; break;
                default: $msg = 'unknown sync status';
            }
        } else {
            $code = -1;
            $msg = 'reply format error';
        }
        return array('code'=>$code, 'msg'=>$msg);
    }

    function sendSocketMsg($host, $port, $arr, &$receive, $bufsize) {
        $checkerror = function($msg) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            die("$msg: [$errorcode] $errormsg");
        };

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or $checkerror('Couldn\'t create socket');
        $result = socket_connect($socket,$host,$port) or $checkerror('Could not connect to server');

        socket_write($socket, $this->tostr($arr), count($arr))  or $checkerror("Could not send data to server");
        $input = socket_read($socket, $bufsize) or $checkerror("Could not read server response");
        $receive = str_split($input);

        for ($i = 0; $i < $bufsize; $i++) {
            $receive[$i] = ord($receive[$i]);
        }
        socket_close($socket);
    }

    //封装一个函数将字节数组转化为整形
    function BytesArrChangeToInt($arr){
        $val=0;
        $n = count($arr);
        for($i=0; $i<$n; $i++){
            $arr[$i]=$arr[$i]<<(($n-$i-1)*8);
            $val+=$arr[$i];
        }
        return $val;
    }

    function getbytes($str) {
        $len = strlen($str);
        $bytes = array();
        for($i=0; $i<$len; $i++) {
            if(ord($str[$i]) >= 128){
                $byte = ord($str[$i]) - 256;
            }else{
                $byte = ord($str[$i]);
            }
            $bytes[] =  $byte ;
        }
        return $bytes;
    }

    function tostr($bytes) {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }
        return $str;
    }

    public function getsocketip() {
        $Conf = M('configure');
        $t = $Conf->where(array('name'=>'local_bind'))->field('stringvalue')->select();
        return $t[0]['stringvalue'];
    }

    public function getsocketport() {
        $Conf = M('configure');
        $t = $Conf->where(array('name'=>'port'))->field('stringvalue')->select();
        return $t[0]['intvalue'];
    }

//  tab 查看同步日志
    public function getlogdir() {
        $t = M('configure')->where(array('name'=>'adlog_path'))->field('stringvalue')->select();
        $logdir = $t[0]['stringvalue'];
        if (is_null($logdir) || !is_dir($logdir)) {
            // $logdir = self::$root . '/resource/logadv';
            $logdir = $_SERVER['DOCUMENT_ROOT'] . __ROOT__;
        }
        $logdir = rtrim($logdir, '\\/') . '/';
        return $logdir;
    }
}