<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:32
 */
namespace Content\Model;
use Think\Model;

class LanguageModel extends Model {
    protected $tableName = 'language';
    protected $fields = array('id', 'language');
    protected $pk = 'id';

    public function getLangNameByID($id) {
        $t = $this->field('language')->where(array('id'=>$id))->select();
        return $t[0]['language'];
    }

    public function getLangList() {
        $condition = 'id != 0';
        return $this->field(array('ID', 'Language'))->where($condition)->select();
    }
}