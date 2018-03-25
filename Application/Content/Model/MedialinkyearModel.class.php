<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:32
 */
namespace Content\Model;
use Think\Model;

class MedialinkyearModel extends Model {
    protected $tableName = 'medialinkyear';
    protected $fields = array('id', 'oid', 'yearid');
    protected $pk     = 'id';

    public function getYearNameByOID($oid) {
        $t = $this->where(array('oid'=>$oid))->field('yearid')->select();
        $yearid = $t[0]['yearid'];

        $Year = new \Content\Model\YearModel();
        $t = $Year->where(array('id'=>$yearid))->field('year')->select();
        return $t[0]['year'];
    }

    public function setyear($data) {
        $data = array_filter($data);
        if (array_key_exists('yearid', $data)) {
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

    public function getyearidbyoid($oid) {
        $fields = array(
            0 => 'yearid',
        );
        $condition = array('oid' => $oid);
        $res = $this->field($fields)->where($condition)->select();
        return $res[0]['yearid'];
    }
}