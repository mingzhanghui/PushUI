<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:34
 */
namespace Content\Model;
use Think\Model;

class GenreModel extends Model {
    protected $tableName = 'genre';
    protected $fields = array('id', 'genre');
    protected $pk = 'id';
    protected $_validate = array(
        array('genre', '', '类型名称已经存在', 0, 'unique', 1)
    );
}