<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 14:10
 */
namespace Content\Model;
use Think\Model;

class PathModel extends Model {
    protected $tableName = 'path';
    protected $fields = array('id', 'oid', 'url', 'asset_id', 'mediatypeid', 'asset_name', 'size', 'date', 'state', 'contenttype');
// '_type'=>array('id'=>'integer','oid'=>'text','url'=>'text','asset_id'=>'text', 'mediatypeid'=>'integer', 'asset_name'=>'text', 'size'=>'integer', 'date'=>'text', 'state'=>'integer', 'contenttype'=>'integer')
    protected $pk     = 'id';
    // protected $connection = 'sqlite://'.__ROOT__.'/db3/MBIS_Server.db3/MBIS_Server#utf8';

    public function getMediaByDate($date) {
        $condition = array(
            'date' => $date,
            'url'  => array('neq', ''),  // 总集url为空不显示
        );
        return $this->field(array('OID', 'url', 'MediaTypeID', 'size'))->where($condition)->select();
    }

    public function existsOID($oid) {
        $where = array('oid' => $oid);
        $t = $this->where($where)->field('oid')->limit(0,1)->select();
        return !is_null($t[0]);
    }

    // 待编辑的媒体文件列表
    public function listMediaByType($MediaTypeID, $pagesize = 16, $page = 1) {
        if (!isset($MediaTypeID)) {
            throw_exception(__METHOD__ . ': MediaTypeID is not set');
        }
        $condition = array(
            'MediaTypeID' => $MediaTypeID,
            'EditStatus' => array('LT', 2), // 编辑状态为已经提交, 或者是已经审核的内容不显示
        );
        return $this
            ->field(array(
                'MBIS_Server_Path.ID AS ID',
                'MBIS_Server_Path.OID AS OID',
                'URL',
                'Asset_Name',
                'MediaTypeID',
                'Size'))
            ->join("MBIS_Server_EditStatus ON MBIS_Server_Path.OID = MBIS_Server_EditStatus.OID", "LEFT")
            ->where($condition)->limit($pagesize * ($page - 1), $pagesize)
            ->order('ID DESC')->select();
    }

    public function getMediaCountByType($MediaTypeID) {
        $condition = array(
            'mediatypeid' => $MediaTypeID,
        );
        return $this->where($condition)->count();
    }

    public function setAssetNameByOID($title, $oid) {
        $where = array("oid" => $oid);
        return $this->where($where)->data(array("asset_name" => $title))->save();
    }

    /**
     * 向$table表中添加$data数组数据
     * @param $table
     * @param $data
     * @param $where  array 检查是否重复的条件, 有重复则什么都不做
     * @return int|mixed
     */
    public function addPathUniq($data, $where) {
        $count = $this->where($where)->count();
        if ($count > 0) {
            return (-1) * $count;
        }
        return $this->data($data)->add();
    }

    /**
     * 生成新的总集OID
     * @return string S2016122800000000000000000000491, S + yyyymmdd + [0...][id]
     */
    public function generateSeriesOID() {
        $maxid = $this->max('id');
        $id = $maxid + 1;
        $format = "S%s%0" . (32 - strlen("S20170313")) . "d";
        return sprintf($format, date("Ymd", time()), $id);
    }

    // 通过分集OID得到总集OID
    public function getsoidbyoid($oid, $mediatypeid) {
        $where = array("oid" => $oid);
        if (!isset($mediatypeid)) {
            $fields = array("mediatypeid");
            $rows = $this->where($where)->field($fields)->select();
            $mediatypeid = $rows[0]['mediatypeid'];
        }
        $soid = null;
        $mediatypeid = intval($mediatypeid);
        switch ($mediatypeid) {
            case 2:
                $Seriesepisode = new \Content\Model\SeriesepisodeModel();
                $soid = $Seriesepisode->getTVSeriesOIDByEpsoid($oid);
                break;
            case 3:
                $Tvshowsepisode = new \Content\Model\TvshowepisodeModel();
                $soid = $Tvshowsepisode->getTVshowsOIDByEpsoid($oid);
                break;
            default: # code... break;
        }
        return $soid;
    }

    /* 通过总集OID 得到分集OID */
    public function listoidbysoid($soid, $mediatypeid) {
        $where = array("oid" => $soid);
        if (!isset($mediatypeid)) {
            $fields = array("mediatypeid");
            $rows = $this->where($where)->field($fields)->select();
            $mediatypeid = $rows[0]['mediatypeid'];
        }
        $oidlist = array();
        $mediatypeid = intval($mediatypeid);
        switch ($mediatypeid) {
            case 5: // 电视剧总集
                $Seriesepisode = new \Content\Model\SeriesepisodeModel();
                $oidlist = $Seriesepisode->listTVSerieEpisodeOID($soid);
                break;
            case 6: // 电视节目总集
                $Tvshowsepisode = new \Content\Model\TvshowepisodeModel();
                $oidlist = $Tvshowsepisode->listTVShowsEpisodeOID($soid);
                break;
            default:
                # code...
                break;
        }
        return $oidlist;
    }

    public function getMediaAbsPath($oid) {
        if (!isset($oid)) {
            \throw_exception(__METHOD__ . ' oid is not set!');
        }
        $Configure = new \Home\Model\ConfigureModel();
        $contentDir = rtrim($Configure->getconfpathbyname('content_path'), "\\/") . "/";

        $rows = $this->where(array("oid" => $oid))->field(array("url"))->select();
        $filename = $rows[0]['url'];
        return array('path' => $contentDir . $filename);
    }

    public function getMediaInfo($oid) {
        $res = array();   // return

        $where = array('oid'=>$oid);
        $fields = array('mediatypeid', 'asset_name');

        $t = $this->where($where)->field($fields)->select();
        // 片名
        $res['asset_name'] = $t[0]['asset_name'];
        // 类型
        $Mediatype = new \Content\Model\MediatypeModel();
        $map = $Mediatype->mapMediaType();
        $mediatypeid = $t[0]['mediatypeid'];
        $res['mediatype'] = $map[ $mediatypeid ];
        // 简介
        $Media = new \Content\Model\MediaModel();
        $t = $Media->field('introduction')->where($where)->select();
        if (is_null($t[0])) {
            // 2, 3, 9 => 电视剧 电视节目 专题节目 (分集)
            $mediatypeid = intval($mediatypeid);
            $where = array('episodeoid'=>$oid);
            switch($mediatypeid) {
                case 2:
                    $table = 'seriesepisode';
                    break;
                case 3:
                    $table = 'tvshowepisode';
                    break;
                case 9:
                    $table = 'specialepisode';
                    break;
                case 4:
                    $table = 'video';
                    $where = array('oid'=>$oid);
                    break;
                default:
                    $table = 'media';
                    $where = array('oid'=>$oid);
            }
            $r = D($table)->field('introduction')->where($where)->select();
            $res['introduction'] = $r[0]['introduction'];
        } else {
            $res['introduction'] = $t[0]['introduction'];
        }
        // 附件
        // 1. url prefix: e.g. /PushUI/resource/appendix/
        $Configure = new \Home\Model\ConfigureModel();
        $prefix = $Configure->getAppendixURLPrefix(); // xxx/
        // 2. mbis_server_appendix +url
        $where = array('attachoid'=>$oid, 'appendixtypeid'=>1);  // 缩略图
        $Appendix = new \Content\Model\AppendixModel();
        $rows = $Appendix->where($where)->field('url')->select();
        if (is_null($rows[0]['url'])) {
            $where['appendixtypeid'] = 2;   // 海报
            $rows = $Appendix->where($where)->field('url')->select();
        }
        $res['url'] = $prefix . $rows[0]['url'];

        return $res;
    }

}