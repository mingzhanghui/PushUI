<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-27
 * Time: 11:10
 */
namespace Content\Model;
use Think\Model;

class SpecialepisodeModel extends Model {
    protected $tableName = 'specialepisode';
    protected $fields = array('id','specialoid','episodeoid','episodeindex');
    protected $pk     = 'id';

    /**
     * 专题节目 总集 已经加入的分集数
     * @param $soid: 总集oid
     */
    public function countSpecialEpisode($soid) {
        if (!isset($soid)) {
            throw_exception(__METHOD__ . " soid is not set!");
        }
        $where = array("specialoid" => $soid);
        return $this->where($where)->count();
    }

    /**
     * 新专题节目分集  (保存草稿)
     * @param $post = array(
     * 'oid'  => xxx,
     * 'soid' => xxx,
     * 'title' => xxx,
     * 'introduction' => xxx,
     * 'episodeindex' => xxx
     * 'attrs' => array(
     *    0 => [5, '数学']
     *    1 => [4, 'php'],
     *   ...
     * );
     *);
     */
    public function setSpecialEpisodeInfo($post) {
        if (!isset($post['oid'])) {
            throw_exception(__METHOD__ . " oid is required");
        }
        $oid = $post['oid'];
        $title = $post['title'];
        // Media
        $data = array("title" => $title, 'introduction' =>  $post['introduction']);
        $where = array("oid" => $oid);
        $Media = new MediaModel();
        $result['media'] = $Media->data($data)->where($where)->save();

        // path
        $path = new PathModel();
        $result['path'] = $path->data(array('asset_name'=>$title))->where($where)->save();

        // specialepisode
        $where = array("episodeoid" => $oid);
        $count = $this->where($where)->count();
        $data = array(
            "specialoid" => $post['soid'],
            "introduction" => $post['introduction'],
            // 当前集数
            "episodeindex" => $post['episodeindex'],
        );
        if (0 == $count) {
            $data['episodeoid'] = $oid;
            $result['specialepisode'] = $this->data($data)->add();
        } else {
            $result['specialepisode'] = $this->data($data)->where($where)->save();
        }

        // speciallinkattr
        $Link = new SpeciallinkattrModel();
        $attrs = $post['attrs'];
        unset($post);
        $result['attr'] = 0; // rows affected

        $Link->where(array("oid" => $oid))->delete();

        foreach ($attrs as $attr) {
            $data = array(
                "oid" => $oid,
                "attrid" => $attr[0],
                "attrval" => $attr[1],
            );
            $result['attr'] += ($Link->data($data)->add() > 0);
        }
        // 编辑状态: 存草稿
        $status = new EditstatusModel();
        $st = $status->getEditStatus($oid);
        if ($st == 0) {
            $result['editstatus'] = $status->setEditStatus($oid, "editstatus", 1);
        } else {
            $result['editstatus'] = 1;
        }

        return $result;
    }

    /**
     * 取得专题节目单集信息
     * @param $oid
     */
    public function getSpecial($oid) {
        if (!isset($oid)) {
            throw_exception(__METHOD__ . ": oid is required");
        }
        $result = array();
        $where = array("oid" => $oid);

        // 单集名
        $media = new MediaModel();
        $fields = array('title', 'introduction');
        $rows = $media->field($fields)->where($where)->select();
        $result['title'] = $rows[0]['title'];
        $result['introduction'] = $rows[0]['introduction'];

        // specialepisode
        $where = array("episodeoid" => $oid);
        $fields = array("specialoid", "episodeindex");
        $rows = $this->field($fields)->where($where)->select();
        $soid = $rows[0]['specialoid'];
        setcookie("soid", $soid, time() + 1800 * 1000);
        $result = array_merge($result, $rows[0]);
        unset($rows);

        // media -> 总集名 title
        $where = array("oid" => $soid);
        $rows = $media->where($where)->field(array("title"))->select();
        $result['stitle'] = $rows[0]['title'];

        // speciallinkattr
        $attrs = array();
        $where = array("oid" => $oid);
        $fields = array("attrid", "attrval");
        $link = new SpeciallinkattrModel();
        $rows = $link->where($where)->field($fields)->select();

        $Attr = new AttrModel();
        foreach ($rows as $row) {
            $id = $row['attrid'];
            $a = $Attr->where(array("id" => $id))->field(array("name"))->select();
            $attr = array(
                'id' => $id,
                'name' => $a[0]['name'],
                'value' => $row['attrval'],
            );
            array_push($attrs, $attr);
        }
        $result['attrs'] = $attrs;
        return $result;
    }

    /**
     * 已经导入的专题节目分集数
     * @param $soid
     * @return mixed
     */
    public function getSpecialsEpisodeCount($soid) {
        $where = array(
            "specialoid" => $soid,
        );
        return $this->field("episodeoid")->where($where)->count();
    }

}