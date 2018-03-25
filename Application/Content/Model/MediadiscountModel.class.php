<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 15:10
 */
namespace Content\Model;
use Think\Model;

class MediadiscountModel extends Model {
    protected $tableName = 'mediadiscount';
    protected $fields = array('mediadiscountid', 'id', 'originalprice', 'datestart', 'dateend', 'discountrate', 'price');
    protected $pk = 'mediadiscountid';

}