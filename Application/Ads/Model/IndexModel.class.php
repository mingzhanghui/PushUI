<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-03
 * Time: 10:17
 */
namespace Ads\Model;
use Think\Model;

class IndexModel extends Model {
    /**
     * 文件类型
     */
    public function listFileTypes() {
        $fields = array('filetypeid as id', 'value as name');
        $map['name'] = array('neq', '未定义');
        $rows = M('AdAdfiletype')->field($fields)->where($map)->select();
        return $rows;
    }

    /**
     * 广告文件相对于项目的目录 (./resource/adv/)
     * configure表中的adv_dir设置应该在项目目录下
     * @return string
     */
    public function getAdvDir() {
        $t = M('configure')->where(array('name'=>'adv_dir'))->field('stringvalue')->select();
        $dir = rtrim($t[0]['stringvalue'], "\\/");
        if (!is_dir($dir)) {
            @unlink($dir);
            mkdir($dir, 0777, true);
        }
        // echo $dir . '<br />';
        $root = $_SERVER['DOCUMENT_ROOT']. __ROOT__;
        $rel = '.' . substr($dir, strlen($root)) . '/';
        // echo $rel;
        return $rel;
    }

    /**
     * 广告类型ID => 广告类型名称
     * @return array 片头广告,挂角广告,暂停广告,滚动字幕广告
     */
    public function mapAdType() {
        $fields = array('adtypeid', 'value');
        $rows = M('AdAdtype')->field($fields)->select();

        $adtypeid = array_column($rows, 'adtypeid');
        $value = array_column($rows, 'value');
        return array_combine($adtypeid, $value);
    }

    /**
     * 可以上传的文件类型后缀
     */
    public function uploadPrefix() {
        $map['value'] = array('IN', array('video','picture'));
        $appendix = M('AdAdfiletype')->field('relativeappendix as a')->where($map)->select();
        $types = array();
        $apps = array_column($appendix, 'a');
        foreach ($apps as $app) {
            $parts = explode(",", $app);
            $types = array_merge($types, $parts);
        }
        return $types;
    }

    /**
     * 广告文件类型ID => 广告文件类型名称 (video, picture, text)
     * @return array
     */
    public function mapAdFileType() {
        $fields = array('filetypeid', 'value');
        $rows = M('AdAdfiletype')->field($fields)->select();

        $filetypeid = array_column($rows, 'filetypeid');
        $value = array_column($rows, 'value');
        return array_combine($filetypeid, $value);
    }

    /**
     * @param $date string "yyyy-mm-dd"
     * @return bool 指定した日付が有効な場合に TRUE、そうでない場合に FALSE を返します。
     */
    public function mycheckdate($date) {
        $a = explode("-", $date);
        if ( 3 == count($a) ) {
            $month = $a[1];
            $day = $a[2];
            $year = $a[0];
            return checkdate($month, $day, $year);
        }
        return false;
    }
}
