<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 15:07
 */
namespace Content\Model;
use Think\Model;

use Content\Common\File;

class MediapriceModel extends Model {
    protected $tableName = 'mediaprice';
    protected $fields = array('id', 'oid', 'price');
    protected $pk = 'id';

    // 电影 戏曲 计费
    public function listMediaCharge($mediatypeid, $page, $pagesize) {
        $where = array(
            'mediatypeid' => $mediatypeid,
            'editstatus'  => array('GT', 1),
            'slicestatus' => 1
        );

        if ($page <=1) {
            $page = 1;
        }
        $media = new MediaModel();
        $join = 'LEFT JOIN mbis_server_editstatus ON mbis_server_editstatus.oid = mbis_server_media.oid';
        $count = $media->join($join)->where($where)->count();

        $totalpage = ceil($count / $pagesize);
        if ($page > $totalpage) {
            $page = $totalpage;
        }
        $fields = array('mbis_server_media.oid as oid', 'title');
        $rows = $media->join($join)->where($where)->page($page, $pagesize)->field($fields)
                      ->order('mbis_server_editstatus.id desc')->select();
        unset($media);

        $path = new PathModel();
        $where['oid'] = '';
        foreach ($rows as $key => $value) {
            $oid = $value['oid'];
            $where['oid'] = $oid;

            $t = $path->where($where)->field('size')->select();
            if (array_key_exists('size', $t[0])) {
                $rows[$key]['size'] = File::humansize( $t[0]['size'] );
            } else {
                $rows[$key]['size'] = 0;
            }

            $t = $this->where($where)->field('price')->select();
            if (array_key_exists('price', $t[0])) {
                $rows[$key]['price'] = $t[0]['price'] / 100;
            } else {
                $rows[$key]['price'] = 0;
            }
        }

        $return = array(
            'page'      => $page,
            'totalpage' => $totalpage,
            'count'     => $count,
            'data'      => $rows
        );
        return $return;
    }

    // 5:电视剧总集, 6:电视节目总集, 8: 专题节目总集
    public function listSeriesCharge($mediatypeid, $page, $pagesize) {
        $where = array(
            'mediatypeid' => $mediatypeid,
        );

        if ($page <=1) {
            $page = 1;
        }
        $media = new MediaModel();
        $count = $media->where($where)->count();

        $totalpage = ceil($count / $pagesize);
        if ($page > $totalpage) {
            $page = $totalpage;
        }
        $fields = array('oid', 'title');
        $rows = $media->where($where)->page($page, $pagesize)
                      ->field($fields)->order('id desc')->select();
        $mediatypeid = intval($mediatypeid);

        $table = '';       // 总集, 分集关联表
        $fs_oid = '';      // 总集oid字段
        $fep_oid = '';     // 分集oid字段
        // $mt_eps = 0;    // 对应分集的mediatypeid

        switch($mediatypeid) {
            case 5:   // 电视剧
                $table = 'seriesepisode';
                $fs_oid = 'seriesoid';
                $fep_oid = 'episodeoid';
                // $mt_eps = 2;
                break;
            case 6:   // 电视节目
                $table = 'tvshowepisode';
                $fs_oid = 'tvshowoid';
                $fep_oid = 'episodeoid';
                // $mt_eps = 3;
                break;
            case 8:   // 专题节目总集
                $table = 'specialepisode';
                $fs_oid = 'specialoid';
                $fep_oid = 'episodeoid';
                // $mt_eps = 9;
                break;
            default:
                throw_exception(__METHOD__. ": unexpected mediatypeid " . $mediatypeid);
        }

        $SE = M($table);
        $Path = new PathModel();
        foreach ($rows as $i => $row) {
            // 分集OID组成的数组
            $rows[$i]['eps'] = array();
            $tmp = $SE->where(array($fs_oid=>$row['oid']))->field($fep_oid)->select();
            while ($elem = each($tmp)) {
                array_push($rows[$i]['eps'], $elem['value'][$fep_oid]);
            }
            unset($tmp);

            $size = 0;
            while ($elem = each($rows[$i]['eps'])) {
                $tmp = $Path->where(array("oid"=>$elem['value']))->field(array('size'))->select();
                $size += $tmp[0]['size'];
                unset($tmp);
            }
            // bug: $size>2G
            $rows[$i]['size'] = File::humansize($size);   // 总集对应的分集大小之和
            unset($rows[$i]['eps']);   // unset分集oid数组

            // 电视剧, 电视节目只能对总集进行定价
            $tmp = $this->where(array('oid'=>$row['oid']))->field(array('price'))->select();
            $rows[$i]['price'] = is_null($tmp[0]['price']) ? 0 : $tmp[0]['price']/100;
        }

        $return = array(
            'page'      => $page,
            'totalpage' => $totalpage,
            'count'     => $count,
            'data'      => $rows
        );
        return $return;
    }

    /**
     * 设定价格
     * @param $oid
     * @param $price   $12.00 -> 1200
     * @return array
     */
    public function setPriceByOID($oid, $price) {
        $price *= 100;   // 价格去掉小数点

        $data = array("price" => $price);
        $where = array("oid" => $oid);
        $rows = $this->where($where)->field(array('price'))->limit(0,1)->select();

        $res = array('id'=>0,'data'=>null, 'count'=>0);
        // 价格条目不存在 +
        if(is_null($rows[0])) {
            $res['id'] = $this->data(array("oid"=>$oid, "price"=>$price))->add();
        } else {  // 价格记录已存在 modify
            $res['count'] = $this->where($where)->save($data);
            if (0 == $price) {  // 价格设置为0, 删除
                $res['count'] = $this->where($where)->delete();
            }
        }

        if ($res['id'] > 0 || $res['count'] > 0) {
            $res['data'] = $price;
        }
        return $res;
    }

    /**
     * 取得价格
     * @param $oid
     * @return mixed
     */
    public function getPriceByOID($oid) {
        $where = array('oid'=>$oid);
        $fields = array('price');
        $rows = $this->where($where)->field($fields)->select();
        return $rows[0]['price']/100;
    }

    /**
     * 根据总集OID 类型ID 取得 已经提交的分集 asset_name + size (esp.8 专题节目总集 + price)
     * @param $oid
     * @param $mediatypeid
     * @return array
     */
    public function listEpisodes($oid, $mediatypeid) {
        $mediatypeid = intval($mediatypeid);

        $table = '';       // 总集, 分集关联表
        $fs_oid = '';      // 总集oid字段
        $fep_oid = '';     // 分集oid字段

        switch($mediatypeid) {
            case 5:   // 电视剧
                $table = 'seriesepisode';
                $fs_oid = 'seriesoid';
                $fep_oid = 'episodeoid';
                break;
            case 6:   // 电视节目
                $table = 'tvshowepisode';
                $fs_oid = 'tvshowoid';
                $fep_oid = 'episodeoid';
                break;
            case 8:   // 专题节目总集
                $table = 'specialepisode';
                $fs_oid = 'specialoid';
                $fep_oid = 'episodeoid';
                break;
            default:
                throw_exception(__METHOD__. ": unexpected mediatypeid " . $mediatypeid);
        }
        $rows = M($table)->where(array($fs_oid=>$oid))->field($fep_oid)->select();
        $oidlist = array();

        $EditStatus = new EditstatusModel();
        $fields = array('editstatus', 'slicestatus');

        // 选择分集OID中 已经备播切分 (&& 已经审核的OID)
        foreach ($rows as $i => $row) {
            $tmp = $EditStatus->where(array('oid' => $row[$fep_oid]))->field($fields)->select();
            if ($tmp[0]['editstatus'] > 1 && $tmp[0]['slicestatus'] != 0) {
                array_push($oidlist, $row[$fep_oid]);
            }
        }
        unset($rows);

        $Path = new PathModel();
        $Media = new MediaModel();
        $fields = array('oid', 'size');
        $result = array();
        // 文件名 大小 价格
        foreach ($oidlist as $eps_oid) {
            $where = array('oid' => $eps_oid);
            $p = $Path->where($where)->field($fields)->select();
            $p[0]['size'] = File::humansize($p[0]['size']);
            $row = $p[0];

            $tmp = $Media->where($where)->field('title')->select();
            if (array_key_exists('title', $tmp[0])) {
                $row['title'] = $tmp[0]['title'];
            } else {
                $row['title'] = $p[0]['asset_name'];
            }
            array_push($result, $row);
        }
        // 专题节目可以给每个分集定价
        if ($mediatypeid == 8) {
            foreach ($result as $i => $item) {
                $tmp = $this->where(array('oid'=>$item['oid']))->field(array('price'))->select();
                $result[$i]['price'] = is_null($tmp[0]['price']) ? 0 : $tmp[0]['price']/100;
                unset($tmp);
            }
        }
        return $result;
    }

}