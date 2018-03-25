<?php
/**
 * Created by PhpStorm.
 * User: mzh
 * Date: 2017-09-08
 * Time: 17:52
 */
namespace Subscribe\Model;
use Content\Model\MediaModel;
use Content\Model\MediapriceModel;
use Think\Model;

class MediasynlogModel extends Model {

    protected $tableName = 'mediasynlog';
    protected $fields = array('id', 'oid', 'mediatypeid', 'size', 'partid', 'partnum', 'status', 'synmark');
    protected $pk = 'id';
    protected $_validate = array(
        array('oid', 'require', '媒体文件OID必须!'),
    );

    /**
     * 内容同步 左边同步进度表格
     * @return array
     */
    public function listContentSync() {
        $Missionlinkmedia = new \Subscribe\Model\MissionlinkmediaModel();
        $linklist = $Missionlinkmedia->listMediaTBD();

        // 文件名 大小 类型ID
        $Path = new \Content\Model\PathModel();
        $fields = array('oid','asset_name as filename', 'size', 'mediatypeid');
        $MediaType = new \Content\Model\MediatypeModel();
        $Price = new MediapriceModel();
        $Media = new MediaModel();

        $rows = array();
        foreach ($linklist as $link) {
            $oid = $link['mediaoid'];
            $where = array('oid' => $oid);
            $t = $Path->where($where)->field($fields)->select();
            $t[0]['size'] = \Content\Common\File::humansize($t[0]['size']);
            $row = $t[0];
            // 内容标题
            $t = $Media->where($where)->field('title')->select();
            if (count($t) > 0) {
                $row['title'] = $t[0]['title'];
            } else {
                $row['title'] = null;
            }

            $t = $MediaType->where(array('id'=>$row['mediatypeid']))->select();
            $row['mediatype'] = $t[0]['mediatype'];
            unset($row['mediatypeid']);

            $row['missionlinkmediaid'] = $link['id'];
            // 同步进度  (? array('missionlinkmediaid' => $link['id']))
            $t = $this->where($where)->field('status')->select();
            $row['status'] = is_null($t[0]['status']) ? 0 : $t[0]['status'];

            // 播发日期
            $row['date'] = $link['date'];

            // 价格
            $t = $Price->where($where)->select();
            $row['price'] = is_null($t[0]['price']) ? 0 : $t[0]['price'];

            array_push($rows, $row);
        }
        return $rows;
    }
}