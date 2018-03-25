<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:13
 */
namespace Content\Model;
use Think\Model;

class EditstatusModel extends Model {
    protected $tableName = 'editstatus';
    protected $fields = array('id', 'oid', 'editstatus', 'slicestatus', 'packagestatus', 'channelstatus', 'datestatus');
    protected $pk     = 'id';

    public function newEditStatus($oid) {
        $data = array(
            'oid' => $oid,
            'editstatus' => 0,
            'slicestatus' => 0,
            'packagestatus' => 0,
            'channelstatus' => 0,
            'datestatus' => 0
        );
        $where = array('oid'=>$oid);
        $t = $this->where($where)->field('oid')->select();
        if (is_null($t[0]['oid'])) {
            return $this->data($data)->add();
        }
        unset($data['oid']);
        return $this->data($data)->where($where)->save();
    }

    public function getEditStatus($oid, $field = 'editstatus') {
        $where = array('oid' => $oid);
        $t = $this->where($where)->field($field)->select();
        return $t[0][$field];
    }

    public function setEditStatus($oid, $field, $status) {
        $where = array('oid' => $oid);
        $data = array(
            $field => $status
        );
        return $this->where($where)->data($data)->save();
    }

    /* 取得一组分集的编辑状态 都审核才算全部完成 */
    public function getgroupeditstatus($oidlist, $field = "slicestatus") {
        if (!in_array($field, array("slicestatus", "editstatus"))) {
            throw_exception(__METHOD__ . ": Unexpected MBIS_Server_EditStatus column");
        }
        $map['oid'] = array("IN", $oidlist);
        $rows = $this->field($field)->where($map)->select();

        $status = 1; // assume slice done
        if ($field == "slicestatus") {
            // 备播状态 1: done, 0: not yet
            foreach ($rows as $row) {
                if (intval($row[$field]) === 0) {
                    $status = 0;
                }
            }
        } else if ($field == "editstatus") {
            // @editstatus: 2: submit; 3: reviewed
            $status = 3; // assume reviewed
            foreach ($rows as $row) {
                if (intval($row[$field]) !== 3) {
                    $status = 2;
                }
            }
        }
        return $status;
    }

    // 审核
    public function review($oid) {
        if (!isset($oid)) {
            \throw_exception(__METHOD__ . ' oid is not set!');
        }
        $result = array();

        $condition = array('oid' => $oid);
        $fields = array("editstatus");
        $res = $this->field($fields)->where($condition)->select();
        $status = intval($res[0]['editstatus']);
        if ($status < 2) {
            $result['code'] = -1;
            $result['msg'] = "ERROR: @media[" . $oid . "] EditStatus=" . $status;
        } else if ($status < 3) {
            $result['code'] = $this->data(array("editstatus" => 3))->where($condition)->save();
            $result['msg'] = 'review success';
        } else {
            $result['code'] = 0;
            $result['msg'] = 'has already reviewed';
        }

        return $result;
    }
}