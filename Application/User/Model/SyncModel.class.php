<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-11
 * Time: 15:41
 */
namespace User\Model;
use Think\Model;

class SyncModel extends Model {
    /**
     * 发送用户同步请求
     */
    public function sendSyncSocket() {
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
        $result[4]=0x00;
        $result[5]=0x00;
        $result[6]=0x07;
        $result[7]=0xd7;

        $result[8]=0;
        $result[9]=0;
        $result[10]=0;
        $result[11]=4;

        $result[12]=0;
        $result[13]=0;
        $result[14]=0;
        $result[15]=1;

        $host = $this->getsocketip();
        $port = $this->getsocketport();

        //receive为接受数据的数组
        $receive=array();
        $this->sendSocketMsg($host, $port,$search,$receive,16);

        // 接受数据是否正确
        $diff = array_diff($receive, $result);

        $a = array_slice($receive, 12, 4);
        $data = $this->BytesArrChangeToInt($a);

        if (count($diff) > 0) {
            $code = -1;
            $msg = '同步失败';
        } else {
            $code = 0;
            $msg = '同步成功';
        }

        return array('code'=>$code, 'data'=>$data, 'msg'=>$msg);
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

}
