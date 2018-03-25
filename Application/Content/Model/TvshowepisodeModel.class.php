<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:12
 */
namespace Content\Model;
use Think\Model;

class TvshowepisodeModel extends Model {
    protected $tableName = 'tvshowepisode';
    protected $fields = array('id', 'tvshowoid', 'episodeoid', 'title','introduction','actor','runtime',
        'episodeindex','thumb', 'thumb', 'theme');
    protected $pk     = 'id';

    /**
     * 已经导入电视节目分集数
     * @param $soid  'SeriesOID' 'S201703140...0xx...x'
     */
    public function getTVShowEpisodeCount($soid) {
        $where = array(
            "tvshowoid" => $soid,
        );
        return $this->field("episodeoid")->where($where)->count();
    }

    /**
     * 根据OID获取电视节目单集信息
     * @param $oid:  电视剧单集 oid
     * @return mixed
     */
    public function TVShowEpisodeInfo($oid) {
        if (!isset($oid)) {
            throw_exception(__METHOD__ . " oid is required");
        }
        // TVShowEpisode
        $where = array("episodeoid" => $oid);
        $fields = array("tvshowoid", "actor", "episodeindex", "theme", "runtime");
        $row = $this->field($fields)->where($where)->limit(0, 1)->select();
        $result = $row[0];
        unset($row);

        // 电视节目总集名称 Media
        $Media = new \Content\Model\MediaModel();
        $where = array('oid' => $oid);
        $fields = array('title', 'introduction');
        $row = $Media->where($where)->field($fields)->select();
        $result = array_merge($result, $row[0]);

        $where = array("oid" => $result['tvshowoid']);
        $fields = array('title');
        $row = $Media->field($fields)->where($where)->limit(0, 1)->select();
        $result['tvshow'] = $row[0]['title'];

        // 电视节目单集备播状态
        $EditStatus = new \Content\Model\EditstatusModel();
        $result['slice'] = $EditStatus->getEditStatus($oid, "slicestatus");

        return $result;
    }

    /**
     * 保存电视节目单集信息 model
     * @param $data
     * e.g. $data['oid'] = "0B4A4FDA862E6B8DD304E3C880EA6EE5"
     */
    public function saveTVShowEpisode($post) {
        $result = array();

        $oid = $post['oid'];
        $title = $post['title'];
        $data['title'] = $title;
        $data["introduction"] = $post['introduction'];
        $media = new MediaModel();
        $where = array('oid' => $oid);
        $media->where($where)->data($data)->save();

        $path = new PathModel();
        $path->where($where)->data(array('asset_name'=>$title))->save();

        // tvshowepisode添加或更新
        $where = array("episodeoid" => $oid);
        $data = array(
            "tvshowoid" => $post['tvshowoid'],
            "actor" => $post['actor'],
            "runtime" => $post['runtime'],
            "theme" => $post['theme'],
        );
        $rows = $this->field(array("episodeoid"))->where($where)->limit(0, 1)->select();
        if (null == $rows[0]) {
            $data = array_merge($data, $where);
            $result['tvshowepisode'] = $this->data($data)->add();
        } else {
            $result['tvshowepisode'] = $this->where($where)->data($data)->save();
        }
        // EditStatus
        $EditStatus = new \Content\Model\EditstatusModel();
        $EditStatus->setEditStatus($oid, 'editstatus', 1);

        return $result;
    }

    /**
     * 电视节目总集 通过总集oid取得分集列表
     * [0] => array(...),
     * [1] => array(...),
     * ...
     */
    public function listTVShowEpisodeBySoid($soid) {
        if (!isset($soid)) {
            throw_exception(__METHOD__ . " series oid soid is not set!");
        }
        $where = array("tvshowoid" => $soid);
        $fields = array("episodeoid", "runtime");
        $result = $this->field($fields)->where($where)->select();

        $n = count($result);
        $Path = new \Content\Model\PathModel();
        for ($i = 0; $i < $n; $i++) {
            $oid = $result[$i]['episodeoid'];
            $where = array("oid" => $oid);
            $row = $Path->field("asset_name")->where($where)->limit(0, 1)->select();
            $result[$i]['title'] = $row[0]['asset_name'];
        }
        return $result;
    }

    /**
     * 电视节目 总集 已经加入的分集数
     * @param $soid: 总集oid
     */
    public function countShowsEpisode($soid) {
        if (!isset($soid)) {
            throw_exception(__METHOD__ . " soid is not set!");
        }
        $where = array("tvshowoid" => $soid);
        return $this->where($where)->count();
    }

    /* 通过电视节目分集OID得到电视节目总集OID */
    public function getTVshowsOIDByEpsoid($oid) {
        $where = array("episodeoid" => $oid);
        $result = $this->where($where)->field("tvshowoid")->select();
        return $result[0]['tvshowoid'];
    }

    public function listTVShowsEpisodeOID($oid) {
        $where = array("tvshowoid" => $oid);
        $fields = array("episodeoid");

        $rows = $this->where($where)->field($fields)->select();
        $result = array();
        foreach ($rows as $row) {
            $result[] = $row['episodeoid'];
        }
        unset($rows);
        return $result;
    }
}