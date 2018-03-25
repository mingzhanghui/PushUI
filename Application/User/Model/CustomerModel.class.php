<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-27
 * Time: 14:07
 */
namespace User\Model;
use Think\Model;

class CustomerModel extends Model {
    protected $tableName = 'customer';
    protected $fields = array('customerid', 'stbid', 'customerdateadded', 'customerstate', 'starttime', 'endtime');
    protected $pk = 'customerid';
    protected $_validate = array(
        array('stbid', 'require', '用户机顶盒ID必须!'),
        array('stbid','','用户机顶盒ID已经存在！',0,'unique',1), // 在新增的时候验证stbid字段是否唯一
    );
    protected $_auto = array (
        array('customerstate','1'),  // 新增的时候把customerstate字段设置为1
        // array('name','getName',3,'callback'), // 对name字段在新增和编辑的时候回调getName方法
        array('customerdateadded', 'getToday', self::MODEL_INSERT, 'callback')
    );

    private function getToday() {
        return date('Y-m-d', time());
    }

    /** STBID检测是否为十六位数字或字母*/
    public static function STBIDCheck($str, $match=null) {
        $pattern = '/^[0-9A-Za-z]{16}$/';
        preg_match($pattern, $str, $match);
        if ($match) {
            $result = strtoupper($match[0]);
            return $result;
        }
        return false;
    }

    /** 时间转化问题*/
    public static function excelTime($date, $time=false) {
        if(is_numeric($date)){
            $jd = gregoriantojd(1, 1, 1970);
            $gregorian = jdtogregorian($jd + intval($date) - 25569);
            $date = explode('/',$gregorian);
            $date_str = str_pad($date[2],4,'0', STR_PAD_LEFT)
                ."-".str_pad($date[0],2,'0', STR_PAD_LEFT)
                ."-".str_pad($date[1],2,'0', STR_PAD_LEFT)
                .($time?" 00:00:00":'');
            return $date_str;
        }
        return $date;
    }

    /*
   * 判断日期格式
   */
    public static function TimeCheck($str, $match=null){
        $patterns = array(
            '((^((1[8-9]\d{2})|([2-9]\d{3}))([-])(10|12|0?[13578])([-])(3[01]|[12][0-9]|0?[1-9])$)|(^((1[8-9]\d{2})|([2-9]\d{3}))([-])(11|0?[469])([-])(30|[12][0-9]|0?[1-9])$)|(^((1[8-9]\d{2})|([2-9]\d{3}))([-])(0?2)([-])(2[0-8]|1[0-9]|0?[1-9])$)|(^([2468][048]00)([-])(0?2)([-])(29)$)|(^([3579][26]00)([-])(0?2)([-])(29)$)|(^([1][89][0][48])([-])(0?2)([-])(29)$)|(^([2-9][0-9][0][48])([-])(0?2)([-])(29)$)|(^([1][89][2468][048])([-])(0?2)([-])(29)$)|(^([2-9][0-9][2468][048])([-])(0?2)([-])(29)$)|(^([1][89][13579][26])([-])(0?2)([-])(29)$)|(^([2-9][0-9][13579][26])([-])(0?2)([-])(29)$))',
            '((^((1[8-9]\d{2})|([2-9]\d{3}))(10|12|0?[13578])(3[01]|[12][0-9]|0?[1-9])$)|(^((1[8-9]\d{2})|([2-9]\d{3}))(11|0?[469])(30|[12][0-9]|0?[1-9])$)|(^((1[8-9]\d{2})|([2-9]\d{3}))(0?2)(2[0-8]|1[0-9]|0?[1-9])$)|(^([2468][048]00)(0?2)(29)$)|(^([3579][26]00)(0?2)(29)$)|(^([1][89][0][48])(0?2)(29)$)|(^([2-9][0-9][0][48])(0?2)(29)$)|(^([1][89][2468][048])(0?2)(29)$)|(^([2-9][0-9][2468][048])(0?2)(29)$)|(^([1][89][13579][26])(0?2)(29)$)|(^([2-9][0-9][13579][26])(0?2)(29)$))',
            '/^[4-9][0-9]{4}$/',
            '((^((1[8-9]\d{2})|([2-9]\d{3}))([\/])(10|12|0?[13578])([\/])(3[01]|[12][0-9]|0?[1-9])$)|(^((1[8-9]\d{2})|([2-9]\d{3}))([\/])(11|0?[469])([\/])(30|[12][0-9]|0?[1-9])$)|(^((1[8-9]\d{2})|([2-9]\d{3}))([\/])(0?2)([\/])(2[0-8]|1[0-9]|0?[1-9])$)|(^([2468][048]00)([\/])(0?2)([\/])(29)$)|(^([3579][26]00)([\/])(0?2)([\/])(29)$)|(^([1][89][0][48])([\/])(0?2)([\/])(29)$)|(^([2-9][0-9][0][48])([\/])(0?2)([\/])(29)$)|(^([1][89][2468][048])([\/])(0?2)([\/])(29)$)|(^([2-9][0-9][2468][048])([\/])(0?2)([\/])(29)$)|(^([1][89][13579][26])([\/])(0?2)([\/])(29)$)|(^([2-9][0-9][13579][26])([\/])(0?2)([\/])(29)$))'
        );

        if(preg_match($patterns[0],$str,$match)) {
            $res = str_replace("-","-",$match[0]);
            return $res;
        } elseif (preg_match($patterns[1],$str,$match)) {
            $year=substr($match[0],0,4);
            $month=substr($match[0],4,2);
            $day=substr($match[0],6,2);
            $match=$year.'-'.$month.'-'.$day;
            return $match;
        } elseif(preg_match($patterns[2], $str, $match)) {
            return self::excelTime($match[0]);
        } elseif(preg_match($patterns[3], $str,$match)) {
            $res=str_replace("/","-",$match[0]);
            return $res;
        }
        return false;
    }

    /**
     * 机顶盒ID必须16位 不足16位后面补0 超出16位后面截断
     * @param $stbid
     */
    public static function formatStbid($stbid) {
        $stbidlen = strlen($stbid);
        if ($stbidlen < 16) {
            $stbid = str_pad($stbid, 16, '0');
        } else if ($stbidlen > 16) {
            $stbid = substr($stbid, 0, 16);
        }
        return $stbid;
    }

    // 2017-1-1 => 2017-01-01
    public static function formatDate($datestr) {
        $parts = explode("-", $datestr);
        $year = sprintf('%04d', array_shift($parts));
        array_walk($parts, function (&$value) {
            $value = sprintf('%02d', $value);
        });
        return $year . '-' . implode("-", $parts);
    }

}