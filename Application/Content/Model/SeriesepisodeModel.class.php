<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:11
 */
namespace Content\Model;
use Think\Model;

class SeriesepisodeModel extends Model {
    protected $tableName = 'seriesepisode';
    protected $fields = array('id', 'seriesoid', 'episodeoid', 'actor','runtime','episodeindex');
    protected $pk     = 'id';

    /**
     * 电视剧分集信息 通过单集oid取得
     * @param $oid:  电视剧单集 oid
     * @return mixed
     */
    public function TVPlayEpisodeInfo($oid) {
        if (!isset($oid)) {
            throw_exception(__METHOD__ . " oid is required");
        }
        // 电视剧总集oid, 分集介绍, 第?集, 单集时长
        $where = array("episodeoid" => $oid);
        $field = array("seriesoid", "episodeindex", "runtime");
        $row = $this->field($field)->where($where)->limit(0, 1)->select();
        $result = $row[0];
        unset($row);

        $Media = new \Content\Model\MediaModel();
        // 分集简介 + 分集标题
        $where = array('oid' => $oid);
        $field = array('introduction',  "title");
        $row = $Media->where($where)->field($field)->select();
        $result['introduction'] = $row[0]['introduction'];
        $result['assetname'] = $row[0]['title'];

        // 电视剧分集所属总集名称
        $soid = $result['seriesoid'];
        $where = array("oid" => $soid);
        $row = $Media->field("title")->where($where)->limit(0, 1)->select();
        $result['title'] = $row[0]['title'];
        unset($row);

        // 总集数
        $Series = new \Content\Model\SeriesModel();
        $where = array("oid" => $soid);
        $row = $Series->field(array("episodes"))->where($where)->limit(0, 1)->select();
        $result['episodes'] = $row[0]['episodes'];
        unset($row);

        // 是否已经备播
        $Editstatus = new \Content\Model\EditstatusModel();
        $result['slice'] = $Editstatus->getEditStatus($oid, 'slicestatus');

        return $result;
    }

    /**
     * 电视剧总集 通过总集oid取得分集列表
     * [0] => array(...),
     * [1] => array(...),
     * ...
     */
    public function listTVPlayEpisodesBySoid($soid) {
        if (!isset($soid)) {
            throw_exception(__METHOD__ . " series oid soid is not set!");
        }
        $where = array("seriesoid" => $soid);
        $fields = array("episodeoid", "runtime", "episodeindex");
        $result = $this->field($fields)->where($where)->select();

        $media = new MediaModel();
        foreach ($result as $key => $value) {
            $t = $media->where(array('oid' => $value['episodeoid']))->field('title')->select();
            $result[$key]['title'] = $t[0]['title'];
        }
        return $result;
    }

    public function listTVSerieEpisodeOID($oid) {
        $where = array("seriesoid" => $oid);

        $rows = $this->where($where)->field('episodeoid')->select();
        $result = array();
        foreach ($rows as $row) {
            $result[] = $row['episodeoid'];
        }
        unset($rows);
        return $result;
    }

    /**
     * 电视剧 总集 已经加入的分集数
     * @param $soid: 总集oid
     */
    public function countSeriesEpisode($soid) {
        $where = array("seriesoid" => $soid);
        return $this->where($where)->count();
    }

    /* 通过电视剧分集OID得到电视剧总集OID */
    public function getTVSeriesOIDByEpsoid($oid) {
        $where = array("episodeoid" => $oid);
        $fields = array("seriesoid");
        $result = $this->where($where)->field($fields)->select();
        return $result[0]['seriesoid'];
    }
}