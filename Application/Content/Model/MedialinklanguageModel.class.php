<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:34
 */
namespace Content\Model;
use Think\Model;

class MedialinklanguageModel extends Model {
    protected $tableName = 'medialinklanguage';
    protected $fields = array('id', 'oid', 'languageid');
    protected $pk     = 'id';

    public function setLang($data) {
        $data = array_filter($data);
        if (array_key_exists('languageid', $data)) {
            $condition = array('oid' => $data['oid']);
            $count = $this->where($condition)->count();
            if ($count > 0) {
                // update
                unset($data['oid']);
                return $this->where($condition)->save($data);

            } else {
                // add
                return $this->data($data)->add();
            }
        }
        return 0;
    }

    public function getlanguageidbyoid($oid) {
        $fields = array(0 => 'languageid');
        $condition = array('oid' => $oid);
        $res = $this->field($fields)->where($condition)->select();
        return $res[0]['languageid'];
    }
}
