<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:31
 */
namespace Content\Model;
use Think\Model;

class MedialinkcountryModel extends Model {
    protected $tableName = 'medialinkcountry';
    protected $fields = array('id', 'oid', 'countryid');
    protected $pk     = 'id';

    public function getCountryNameByOID($oid) {
        $res = $this->field(array('countryid'))->where(array("oid" => $oid))->select();
        $countryid = $res[0]['countryid'];

        $Country = new \Content\Model\CountryModel();
        $res = $Country->field(array('country'))->where(array("id" => $countryid))->select();
        return $res[0]['country'];
    }

    public function setcountry($data) {
        $data = array_filter($data);
        if (array_key_exists('countryid', $data)) {
            $condition = array('oid' => $data['oid']);
            $count = $this->where($condition)->count();
            if ($count > 0) {
                unset($data['oid']);
                return $this->where($condition)->save($data);
            }
            return $this->data($data)->add();
        }
        return 0;
    }

    public function getcountryidbyoid($oid) {
        $fields = array(
            0 => 'countryid',
        );
        $condition = array('oid' => $oid);
        $res = $this->field($fields)->where($condition)->select();
        return $res[0]['countryid'];
    }

}