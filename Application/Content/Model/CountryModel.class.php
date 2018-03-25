<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-28
 * Time: 14:19
 */
namespace Content\Model;
use Think\Model;

class CountryModel extends Model {
    protected $tableName = 'country';
    protected $fields = array('id', 'country');
    protected $pk = 'id';

    // MBIS_Server_Country
    public function getCountryList() {
        $condition = 'id != 0';
        return $this->field(array('id', 'country'))->where($condition)->select();
    }
}