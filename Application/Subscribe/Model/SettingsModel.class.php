<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-10
 * Time: 15:01
 */
namespace Subscribe\Model;
use Think\Model;

class SettingsModel extends Model {

    public function listCycleType() {
        $cycletype = M('updatecycletype');
        $fields = array('id', 'updatecycletype as type', 'updatecycledescription as desc');
        return $cycletype->field($fields)->order('id desc')->select();
    }

    public function addCycleType($data) {
        $cycletype = M('updatecycletype');
        $res = array();
        if ($cycletype->create($data)) {
            if ($cycletype->add()) {
                $res['code'] = $this->getLastInsID();
                $res['msg'] = 'add UpdateCycleType success';
            } else {
                $res['code'] = -1;
                $res['msg'] = 'add failed';
            }
        } else {
            $res['code'] = -2;
            $res['msg'] = 'create object failed';
        }
        return $res;
    }

    public function delperiodtype($id) {
        $cycletype = M('updatecycletype');
        return $cycletype->where('id='.$id)->delete();
    }

    public function editperiodtype($id, $data) {
        $cycletype = M('updatecycletype');
        $where =array('id' => $id);
        return $cycletype->where($where)->save($data);
    }

}