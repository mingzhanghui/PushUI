<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-27
 * Time: 11:08
 */
namespace Content\Model;
use Think\Model;

class VideoModel extends Model {
    protected $tableName = 'video';
    protected $fields = array('id','oid','title','resource','bftime','introduction','thumb','date');
    protected $pk     = 'id';

    public function getVideoInfo($oid) {
        $where = array("oid" => $oid);
        $fields = array("title", "resource", "bftime", "introduction");
        $rows = $this->field($fields)->where($where)->select();
        $result = $rows[0];

        if (is_null($result['title'])) {
            $media = new MediaModel();
            $t = $media->where($where)->field('title')->select();
            if (count($t)>0) {
                $result['title'] = $t[0]['title'];
            }
        }

        // SliceStatus	Int	分片状态，0 未分片 1已分片SliceStatus	Int	分片状态，0 未分片 1已分片
        $EditStatus = new \Content\Model\EditstatusModel();
        $result['slicestatus'] = $EditStatus->getEditStatus($oid, 'slicestatus');

        return $result;
    }
}