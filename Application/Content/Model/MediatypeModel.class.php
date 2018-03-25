<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:09
 */
namespace Content\Model;
use Think\Model;

class MediatypeModel extends Model {
    protected $tableName = 'mediatype';
    protected $fields = array('id', 'mediatype');
    protected $pk     = 'id';

    public function getMediaTypeById($id) {
        $res = $this->find($id);
        return $res['mediatype'];
    }

    /**
     * id => name
     */
    public function mapMediaType() {
        $rows = $this->field(array('id','mediatype'))->select();

        $map = array();
        foreach ($rows as $row) {
            $map[ $row['id'] ] = $row['mediatype'];
        }
        unset($rows);
        return $map;
    }

    /**
     * 去掉总集的所有类型列表, select > option
     */
    public function getFileTypes() {
        $where = "mediatype not like '%总集' and mediatype!='待定'";
        $res = $this->where($where)->select();
        return $res;
    }

    /**
     * @return array [1, 2, 3, 4, 7]
     */
    public function listMediaTypeID() {
        $where = "mediatype not like '%总集' and mediatype!='待定'";
        $res = $this->where($where)->field('id')->select();
        $a = array();
        foreach ($res as $row) {
            $a[] = $row['id'];
        }
        return $a;
    }

    public function listMediaTypes() {
        $fields = array("id, mediatype");
        $map['id'] = array('gt', 0);
        $rows = $this->field($fields)->where($map)->order('id desc')->select();
        return $rows;
    }
}