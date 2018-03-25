<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:35
 */
namespace Content\Model;
use Think\Model;

class MedialinktagModel extends Model {
    protected $tableName = 'medialinktag';
    protected $fields = array('id', 'oid', 'tagid');
    protected $pk     = 'id';

    public function addMediaTag($oid, $tagid) {
        $where = array('oid'=> $oid);
        $count = $this->where($where)->count();
        $data['tagid'] = $tagid;
        if ($count > 0) {
            $n = $this->where($where)->data($data)->save();
        } else {
            $data['oid'] = $where['oid'];
            $n = $this->data($data)->add();
        }
        return $n;
    }

    // MBIS_Server_MedialinkTag
    public function setTag($data) {
        $data = array_filter($data);
        if (array_key_exists('tagid', $data)) {
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

    public function gettagidbyoid($oid) {
        $fields = array(
            0 => 'tagid',
        );
        $condition = array('oid' => $oid);
        $res = $this->field($fields)->where($condition)->select();
        return $res[0]['tagid'];
    }
}