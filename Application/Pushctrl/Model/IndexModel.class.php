<?php
/**
 * Created by vscode.
 * User: mzh
 * Date: 2017-04-25
 * Time: 18:08
 */
namespace Pushctrl\Model;
use Think\Model;

class IndexModel extends Model {

    public function listPush() {
        $PushStatus = M('pushstatus');
        $Package = M('package');
        $Mission = M('mission');
        $Path = M('path');
        $Link = M('missionlinkmedia');

        // missionlinkmediaid: MissionlinkMedia表的ID
        $fields = array('oid', 'roundcount', 'ratio', 'packageid', 'missionid', 'missionlinkmediaid');
        // 该内容的播发状态: 0 - 未播发 / 1 - 正在播发或完成
        $where = array('state' => 1);

        $rows = $PushStatus->where($where)->field($fields)->select();

        $mediatype = $this->mapMediaType();

        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            // 总轮次
            $t = $Link->where(array('id'=>$rows[$i]['missionlinkmediaid']))->field('round')->select();
            $rows[$i]['round'] = $t[0]['round'];
            // 进度
            $rows[$i]['ratio'] = $rows[$i]['ratio'] / 100;
            // 业务包名
            $t = $Package->where(array('id'=>$rows[$i]['packageid']))->field('packagename')->select();
            $rows[$i]['packagename'] = $t[0]['packagename'];
            // 业务期名
            $t =$Mission->where(array('id'=>$rows[$i]['missionid']))->field('missionname')->select();
            $rows[$i]['missionname'] = $t[0]['missionname'];
            // 内容名称
            $t = $Path->where(array('oid'=>$rows[$i]['oid']))->field(array('asset_name', 'mediatypeid', 'size'))->select();
            $rows[$i]['asset_name'] = $t[0]['asset_name'];
            $rows[$i]['mediatypeid'] = $t[0]['mediatypeid'];
            $rows[$i]['size'] = humansize($t[0]['size']);
            // 类型
            $rows[$i]['mediatype'] = $mediatype[ $rows[$i]['mediatypeid'] ];
        }
        return $rows;
    }

    public function getMediaInfo($oid) {
        $res = array();   // return

        $where = array('oid'=>$oid);
        $fields = array('mediatypeid', 'asset_name');

        $t = M('path')->where($where)->field($fields)->select();
        // 片名
        $res['asset_name'] = $t[0]['asset_name'];
        // 类型
        $map = $this->mapMediaType();
        $mediatypeid = $t[0]['mediatypeid'];
        $res['mediatype'] = $map[ $mediatypeid ];
        // 简介
        $t = M('media')->field('introduction')->where($where)->select();
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
            $r = M($table)->field('introduction')->where($where)->select();
            $res['introduction'] = $r[0]['introduction'];
        } else {
            $res['introduction'] = $t[0]['introduction'];
        }
        // 附件
        // 1. url prefix: e.g. /PushUI/resource/appendix/
        $model = new \Content\Model\EditModel();
        $prefix = $model->getAppendixURLPrefix();
        // 2. mbis_server_appendix +url
        $where = array('attachoid'=>$oid, 'appendixtypeid'=>1);  // 缩略图
        $Appendix = M('appendix');
        $rows = $Appendix->where($where)->field('url')->select();
        if (is_null($rows[0]['url'])) {
            $where['appendixtypeid'] = 2;   // 海报
            $rows = $Appendix->where($where)->field('url')->select();
        }
        $res['url'] = $prefix . $rows[0]['url'];

        return $res;
    }

    /**
     * 返回id => mediatype 关联数组
     */
    public function mapMediaType() {
        $map = array();
        $rows = M('mediatype')->select();
        foreach ($rows as $row) {
            $map[ $row['id'] ] = $row['mediatype'];
        }
        unset($rows);
        return $map;
    }

}