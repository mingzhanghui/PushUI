<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-03-17
 * Time: 11:04
 */
namespace Home\Model;
use Think\Model;

/**
 * Class ServerModel:　UI-Server
 * @package Home\Model
 */
class ServerModel extends Model {
    /**
     * 备播入口函数
     * @param $OID
     * @return array|int
     */
    public function cutPart($OID) {
        //组装发送帧
        $search = array(
            100,  117,  116,   97,
            0x00, 0x00, 0x03,  0xea,
            0,    0,    0,     0x40,
        );
        for ($i = count($search); $i < 44; $i++) {
            $search[$i] = 0;
        }

        $offset = count($search);

        $arr = $this->strToAscii($OID);
        $len = strlen($OID);
        for($i=0; $i < $len; $i++) {
            $search[$i + $offset] = $arr[$i];
        }

        //应该接收到的数据
        $exp = array(
            100, 117, 116, 114,
            0,   0,   7,  -46,
            0,   0,   0,   8,
            0,   0,   0,   1
        );
        //还有4个字节为切分后的文件数未知

        $config = new \Home\Model\ConfigureModel();
        $host = $config->getconfstrbyname("local_bind");
        $port = $config->getconfintbyname("port");

        //receive为接受数据的数组
        $receive = array();
        $res = $this->sendSocketMsg($host, $port, $search, $receive, 20);
        if ($res['code'] !== 0) {
            return $res;
        }
        //接受数据是否正确
        $temp = 0;  // success
        for($i=0; $i<16; $i++) {
            if(!($receive[$i] == $exp[$i])) {
                $temp = -1;  // fail
                $res['msg'] = 'receive data error';
            }
        }
        $res['code'] = $temp;
        return $res;
    }

    // 封装一个函数将字节数组转化为整形
    public function BytesArrChangeToInt($arr) {
        $val = 0;
        $n = count($arr);
        for($i = 0; $i < $n; $i++){
            $arr[$i] = $arr[$i] << (($n - $i - 1) * 8);
            $val += $arr[$i];
        }
        return $val;
    }

    // 定义一个函数将字符串转化为ascii码数组
    function strToAscii($str){
        $arr=array();
        for($i=0; $i<strlen($str); $i++) {
            $temp = substr($str,$i,1);
            $arr[$i] = ord($temp);
        }
        return $arr;
    }

    public function getbytes($str) {
        $len = strlen($str);
        $bytes = array();
        for($i = 0; $i < $len; $i++) {
            if(ord($str[$i]) >= 128) {
                $byte = ord($str[$i]) - 256;
            } else {
                $byte = ord($str[$i]);
            }
            $bytes[] =  $byte ;
        }
        return $bytes;
    }

    public function tostr($bytes) {
        $str = '';
        foreach($bytes as $ch) {
            $str .= chr($ch);
        }
        return $str;
    }

    public function sendSocketMsg($host, $port, $arr, &$receive, $receivenum) {
        $res = array('code'=>0, 'msg'=>'success');
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket < 0) {
            $errorcode = socket_last_error();
            $res['code'] = (-1) * $errorcode;
            $res['msg'] = sprintf('socket_create filed: [%d]', $errorcode);
            return $res;
        }
        $result = socket_connect($socket, $host, $port);
        if ($result == false) {
            $errorcode = socket_last_error();
            // $errormsg = socket_strerror($errorcode);

            $res['msg'] = ("socket_connect failed: [$errorcode]");
            $res['code'] = (-1) * $errorcode;
        }

        socket_write($socket, $this->tostr($arr), count($arr));

        $input = socket_read($socket,$receivenum);
        $receive = $this->getbytes($input);
        socket_close($socket);
        return $res;
    }

}