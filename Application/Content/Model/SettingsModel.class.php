<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-11
 * Time: 17:12
 */
namespace Content\Model;
use Think\Model;

class SettingsModel extends Model {
    public function listMediaTypes() {
        $fields = array("id, mediatype");
        $map['id'] = array('gt', 0);
        $rows = M("mediatype")->field($fields)->where($map)->order('id desc')->select();
        return $rows;
    }

    public function listAppendixTypes() {
        $fields = array("id, appendixtype");
        $rows = M("appendixtype")->field($fields)->order('id desc')->select();
        return $rows;
    }

    public function listGenreTypes() {
        $fields = array("id, genre");
        $rows = M("genre")->field($fields)->order('id desc')->select();
        return $rows;
    }

    public function listCountry() {
        $fields = array("id, country");
        $rows = M("country")->field($fields)->order('id desc')->select();
        return $rows;
    }

    public function listYear() {
        $fields = array("id, year");
        $rows = M("year")->field($fields)->order('id desc')->select();
        return $rows;
    }

    // @type: "genre"/"country"/"year"
    public function countMediaByTypeID($type, $id) {
        $table = "medialink" . $type;
        $field = $type . "id";
        $where = array($field => $id);
        return M($table)->where($where)->count();
    }

    public function delType($type, $id) {
        return M($type)->where(array("id"=>$id))->delete();
    }

    /**
     * 返回内容管理 所有系统设置配置项 key=>value (pair)
     * @return array (
     *  'content_path'  => xxx,
     *  'appendix_path' => xxx,
     *  ...
     * )
     */
    public function listConfig() {
        $entries = array(
            // 内容存放路径 D:\software\wamp\www\mibs\UI\test_pushed\resource\media
            array("content_path" => 'stringvalue'),
            // 附件的存放路径 D:\software\wamp\www\mibs\UI\test_pushed\resource\appendix
            array("appendix_path" => 'stringvalue'),
            // 缩略图存放路径 D:\software\wamp\www\mibs\UI\test_pushed\resource\thumb
            array("thumb_path" => 'stringvalue'),
            // 背景图存放路径 D:\software\wamp\www\mibs\UI\test_pushed\resource\background
            array("background_path" => 'stringvalue'),
            // 缩略图的宽度值 132
            array("thumb_width"=>'intvalue'),
            // 缩略图的高度值 96
            array("thumb_height"=>'intvalue'),
            // 背景图的宽度值 178
            array("background_width"=>'intvalue'),
            // 背景图的高度值 250
            array("background_height"=>'intvalue')
        );
        $Config = M("configure");
        $result = array();

        $fields = array('intvalue', 'stringvalue');
        foreach ($entries as $entry) {
            foreach ($entry as $key => $value) {
                $where = array("name" => $key);
                $rows = $Config->where($where)->field($fields)->select();
                $result[$key] = $rows[0][$value];
            }
        }

        return $result;
    }
}