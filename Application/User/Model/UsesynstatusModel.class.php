<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-27
 * Time: 16:43
 */
namespace User\Model;
use Think\Model;

class UsesynstatusModel extends Model {
    protected $tableName = 'usesynstatus';
    protected $fields = array('id', 'status');
    protected $pk = 'id';
    protected $_validate = array(
        array('status', 'require', '用户同步状态必须!')
    );
    protected $_auto = array ();

    public function reset() {
        $fields = array('id', 'status');
        $t = $this->field($fields)->order('id desc')->limit(0,1)->select();
        if (count($t) > 0) {
            $id = $t[0]['id'];
            $c = $this->where( array('id' => $id) )->data( array('status' => 0) )->save();
        } else {
            $c = 0;
        }
        return $c;
    }

}