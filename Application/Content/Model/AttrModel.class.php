<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-27
 * Time: 11:19
 */
namespace Content\Model;
use Think\Model;

class AttrModel extends Model {
    protected $tableName = 'attr';
    protected $fields = array('id', 'name');
    protected $pk = 'id';
}