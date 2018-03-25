<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-02
 * Time: 13:10
 */
namespace Pushctrl\Model;
use Think\Model;

class SystemModel extends Model {
    function getbytes($str) {

        $len = strlen($str);
        $bytes = array();
        for($i=0;$i<$len;$i++) {
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

    function sendSocketMsg($host,$port,$arr,$back,&$receive,$receivenum){
        $socket = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
        if ($socket < 0){
            return false;
        }
        $result = socket_connect($socket,$host,$port);
        if ($result == false){
            return false;
        }
        socket_write($socket,$this->tostr($arr),count($arr));
        if($back!=0){
            $input = socket_read($socket,$receivenum);
            $receive=$this->getbytes($input);
            socket_close ($socket);
            return true;
        }else{
            socket_close($socket);
            return true;
        }
    }

    public function SendTbSocket() {
        //组装发送帧
        $search=array();
        $search[0]=100;
        $search[1]=117;
        $search[2]=116;
        $search[3]=97;
        $search[4]=0x00;
        $search[5]=0x00;
        $search[6]=0x03;
        $search[7]=0xF0;
        $search[8]=0;
        $search[9]=0;
        $search[10]=0;
        $search[11]=0X20;
        $search[12]=0;
        $search[13]=0;
        $search[14]=0;
        $search[15]=0;
        $search[16]=0;
        $search[17]=0;
        $search[18]=0;
        $search[19]=0;
        $search[20]=0;
        $search[21]=0;
        $search[22]=0;
        $search[23]=0;
        $search[24]=0;
        $search[25]=0;
        $search[26]=0;
        $search[27]=0;
        $search[28]=0;
        $search[29]=0;
        $search[30]=0;
        $search[31]=0;
        $search[32]=0;
        $search[33]=0;
        $search[34]=0;
        $search[35]=0;
        $search[36]=0;
        $search[37]=0;
        $search[38]=0;
        $search[39]=0;
        $search[40]=0;
        $search[41]=0;
        $search[42]=0;
        $search[43]=0;
        //应该接收到的数据
        $result=array();
        $result[0]=100;
        $result[1]=117;
        $result[2]=116;
        $result[3]=114;
        $result[4]=0;
        $result[5]=0;
        $result[6]=7;
        $result[7]=-40;
        $result[8]=0;
        $result[9]=0;
        $result[10]=0;
        $result[11]=4;
        $result[12]=0;
        $result[13]=0;
        $result[14]=0;
        $result[15]=1;

        //receive为接受数据的数组
        $receive=array();
        $host = $this->getsocketip();
        $port = $this->getsocketport();
        $this->sendSocketMsg($host, $port, $search,1,$receive,16);
        //接受数据是否正确
        $temp=true;
        for($i=0;$i<16;$i++){
            if(!($receive[$i]==$result[$i])){
                $temp=false;
            }
        }
        if($temp) {
            $SendTbSocketRes=1;
        } else {
            $SendTbSocketRes=0;
        }
        /// echo "<script>alert('同步成功')</script>";
        /// $this->tpl->display("tpl/pushcontrol/xt");
        return $SendTbSocketRes;
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

