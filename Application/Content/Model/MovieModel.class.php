<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 15:21
 */
namespace Content\Model;
use Think\Model;

class MovieModel extends Model {
    protected $tableName = 'movie';
    protected $fields = array('id', 'oid', 'director', 'actor', 'runtime');
    protected $pk     = 'id';

    public function getMovie($oid) {
        $fields = array(
            0 => 'director',
            1 => 'actor',
            2 => 'runtime',
        );
        $condition = array('oid' => $oid);
        $res = $this->field($fields)->where($condition)->select();
        return $res[0];
    }
}