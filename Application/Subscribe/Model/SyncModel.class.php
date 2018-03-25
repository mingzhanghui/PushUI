<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-24
 * Time: 15:05
 */
namespace Subscribe\Model;
use Boris\Config;
use Home\Model\ConfigureModel;
use Think\Model;

class SyncModel extends Model {

    /**
     * 内容同步 左边同步进度表格
     * @return array
     */
    public function listContentSync() {
        $oidlist = $this->listMediaTbd();

        // 文件名 大小 类型ID
        $Path = M('path');
        $fields = array('oid','asset_name as filename', 'size', 'mediatypeid');
        $MediaType = M('mediatype');
        $Price = M('mediaprice');
        $Log = M('mediasynlog');

        $rows = array();
        foreach ($oidlist as $oid) {
            $where = array('oid' => $oid);
            $t = $Path->where($where)->field($fields)->select();
            $t[0]['size'] = humansize($t[0]['size']);
            $row = $t[0];

            $t = $MediaType->where(array('id'=>$row['mediatypeid']))->select();
            $row['mediatype'] = $t[0]['mediatype'];
            unset($row['mediatypeid']);

            // 价格
            $t = $Price->where($where)->select();
            $row['price'] = is_null($t[0]['price']) ? 0 : $t[0]['price'];

            // 同步进度
            $t = $Log->where($where)->field('status')->select();
            $row['status'] = is_null($t[0]['status']) ? 0 : $t[0]['status'];

            array_push($rows, $row);
        }
        return $rows;
    }

    /**
     * 今天及以后播发的媒体内容
     */
    public function listMediaTbd() {
        $Link = M('missionlinkmedia');
        $today = date('Y-m-d', time());
        $map['date'] = array('EGT', $today);
        $rows = $Link->where($map)->field('mediaoid')->order('id desc')->select();

        $res = array();
        foreach ($rows as $row) {
            $res[] = $row['mediaoid'];
        }
        return $res;
    }

    /**
     * 内容同步右边表格 未过期的业务期
     */
    public function listMission() {
        $Mission = M('mission');
        $today = date('Y-m-d', time());
        $map['enddate'] = array('EGT', $today);

        // State	    Int	记录备播状态；0-未备播，1-已备播
        // VersionID	Int	业务期的当前版本号默认值为1
        // SynVersionID	Int	最后一次同步成功时业务期的版本号。默认值为0
        $fields = array('id as missionid', 'missionname', 'state', 'versionid', 'synversionid');
        $rows = $Mission->where($map)->field($fields)->select();

        $n = count($rows);
        $Link = M('packagelinkmission');
        $Package = M('package');
        $fields = array('packagename', 'price');

        for ($i = 0; $i < $n; $i++) {
            $where = array('missionid' => $rows[$i]['missionid']);
            // $rows[$i]['packageid'] =
            $t = $Link->where($where)->field('packageid')->select();
            $rows[$i]['packageid'] = $t[0]['packageid'];

            $t = $Package->where(array('id'=>$rows[$i]['packageid']))->field($fields)->select();
            $rows[$i]['packagename'] = $t[0]['packagename'];
            $rows[$i]['price'] = $t[0]['price'];
        }
        return $rows;
    }

    /**
     * UI向UI-Server请求同步数据到播发服务器
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
        $result[4] = 0x00;
        $result[5] = 0x00;
        $result[6] = 0x07;
        $result[7] = 0xd3;
        // 0 -> 4Bytes
        for ($i = 8; $i < 12; $i++) {
            $result[$i] = 0;
        }

        $ip = $this->getsocketip();
        $port = $this->getsocketport();
        //receive为接受数据的数组
        $receive = array();
        $this->sendSocketMsg($ip, $port, $sync, $receive, 12);

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
     * 发送查询Socket帧
     * @return array
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
        $result[7] = 0xd4;
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
        $ip = $this->getsocketip();
        $port = $this->getsocketport();

        $receive=array();
        define('BUFSIZE', 16);
        $this->sendSocketMsg($ip, $port, $search, $receive, BUFSIZE);
        $diff = array_diff(array_slice($receive, 0, 12), $result);

        if (0===count($diff)) {
            $a = array_slice($receive, 12, 4);
            $code = $this->BytesArrChangeToInt($a);
            switch ($code) {
                case 0: $msg = 'sync done'; break;
                case 1: $msg = 'sync ongoing'; break;
                case 2: $msg = 'sync error'; break;
                default: $msg = 'unknown sync status';
            }
        } else {
            $code = -1;
            $msg = 'reply format error';
        }
        return array('code'=>$code, 'msg'=>$msg);
    }

    public function getsocketip() {
        $Conf = new ConfigureModel();
        $t = $Conf->where(array('name'=>'local_bind'))->field('stringvalue')->select();
        return $t[0]['stringvalue'];
    }

    public function getsocketport() {
        $Conf = new ConfigureModel();
        $t = $Conf->where(array('name'=>'port'))->field('intvalue')->select();
        return $t[0]['intvalue'];
    }

    //////////////////////////////////////////////////////////////////////////////////////
    //                              utilities                                           //
    //////////////////////////////////////////////////////////////////////////////////////
    /**
     * socket read/write
     * @param $host
     * @param $port
     * @param $arr   array(byte)
     * @param $receive
     * @param $bufsize
     */
    public function sendSocketMsg($host, $port, $arr, &$receive, $bufsize) {
        // header("Content-type: text/html; charset=utf-8");
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
    /**
     * byte array to integer
     */
    function BytesArrChangeToInt($arr) {
        $val = 0;
        $n = count($arr);
        for($i=0; $i<$n; $i++) {
            $val <<= 8;
            $val += $arr[$i];
        }
        return $val;
    }
    /**
     * string to byte array
     * @param $str
     * @return array
     */
    function getbytes($str) {
        $len = strlen($str);
        $bytes = array();
        for($i=0; $i<$len; $i++) {
            if (ord($str[$i]) >= 128) {
                $byte = ord($str[$i]) - 256;
            } else {
                $byte = ord($str[$i]);
            }
            array_push($bytes, $byte);
        }
        return $bytes;
    }

    /**
     * byte array to string
     * @param $bytes
     * @return string
     */
    function tostr($bytes) {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }
        return $str;
    }
    // END UTILITIES

    // D:/software/wamp/www/PushUI/resource/logcontent
    public function getLogDir() {
        $t = M('configure')->where(array('name'=>'log_path'))->field('stringvalue')->select();
        $dir = $t[0]['stringvalue'];
        if (is_null($dir)) {
            $dir = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/resource/logcontent';
            mkdir($dir, 777, true);
            return $dir;
        }
        $dir = rtrim($dir, '\\/');
        if (!is_dir($dir)) {
            @unlink($dir);
            mkdir($dir, 777, true);
        }
        return $dir;
    }
}