<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:33
 */
namespace Content\Model;
use Think\Model;

class YearModel extends Model {
    protected $tableName = 'year';
    protected $fields = array('id', 'year');
    protected $pk = 'id';

    protected $_validate = array(
        array('year','require','年份名称必须！'), //默认情况下用正则进行验证
        array('year','','年份已经存在！', self::EXISTS_VALIDATE, 'unique', self::MODEL_BOTH), // 在新增和修改的时候验证year字段是否唯一
    );
}