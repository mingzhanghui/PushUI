<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-27
 * Time: 11:16
 */
namespace Content\Model;
use Think\Model;

class SpeciallinkattrModel extends Model {

    protected $tableName = 'speciallinkattr';
    protected $fields = array('id', 'oid', 'attrid', 'attrval');
    protected $pk = 'id';

}