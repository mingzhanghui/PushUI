<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 15:17
 */
namespace Subscribe\Model;
use Pushctrl\Model\PushstatusModel;
use Think\Model;

use \Content\Common\File;

class MissionlinkmediaModel extends Model {

    protected $tableName = 'missionlinkmedia';
    protected $fields = array('id', 'missionid', 'mediaoid', 'round', 'priority', 'date', 'state');
    protected $pk = 'id';
    protected $_validate = array(
        array('missionid', 'require', '业务期必须!'),
        array('mediaoid', 'require', '媒体文件必须!'),
        array('date','require','播发日期必须！'), //默认情况下用正则进行验证
    );

    /**
     * 添加一条内容的到期内容 检查日期, 播发计划是否重复
     * @param $data
     * array (
     *   'mediaoidlist' => array (
     *     0 => 'CF47F40C5B9676F1CEE45F1021BA9800'
     *     1 => '0B4A4FDA862E6B8DD304E3C880EA6EE5'
     *     2 => 'FC1D25E6626669E91897D2D5083AEFF8'
     *    ),
     *   'date' => '2017-04-18',
     *   'round' => 1
     *);
     */
    public function checkAddContent($data) {
        $result = array('code'=>0,'error'=>'','data'=>null);

        $fields = array('missionid', 'mediaoidlist', 'date');
        foreach ($fields as $field) {
            !array_key_exists($field, $data)
            && throw_exception(__METHOD__ . " column [" . $field . "] is missing");
        }
        $Mission = new \Subscribe\Model\MissionModel();
        $range = $Mission->getMissionDateRange($data['missionid']);
        $date = strtotime($data['date']);   // to check
        $start = strtotime($range['startdate']);
        $end = strtotime($range['enddate']);

        if ($date < $start || $date > $end) {
            $result['code'] = -1;
            $result['error'] = "播发日期不在业务期日期范围内!";
            return $result;
        }

        // $missionid = $data['missionid'];
        $date = $data['date'];

        $sizesum = 0;
        $Path = new \Content\Model\PathModel();
        foreach ($data['mediaoidlist'] as $mediaoid) {
            $where = array(
                // 'missionid' => $missionid,
                'mediaoid'  => $mediaoid,
                'date'      => $date
            );
            $count = $this->where($where)->count();
            if ($count > 0) {
                $result['code'] = -2;
                // $result['error'] = "已经存在相同的播发计划: missionid=".$missionid ;
                $result['error'] = "该内容在".$date."的播发日程中";
                return $result;
            }

            $t = $Path->where(array('oid'=>$mediaoid))->field('size')->select();
            $sizesum += $t[0]['size'];
        }
        // 今日既存播发总量
        $sizesum += $this->getTodayMediaSize();

        // check 播发总量 error
        $sizesum /= 1024 * 1024 * 1024; // G
        $Configure = new \Home\Model\ConfigureModel();
        $quota = $Configure->getQuota();
        if ($quota < $sizesum) {
            $result['code'] = -3;
            $result['error'] = '播发计划已经超出当日播发总量';
            return $result;
        }

        $result['data'] = true;
        $result['error'] = 'OK';

        return $result;
    }

    /**
     * 添加一条内容的到期内容, 并更新版本号
     */
    public function doAddContent($input) {
        $result = array('code'=>0,'error'=>'','data'=>null);

        $missionid = $input['missionid'];
        $round = $input['round'];
        $date = $input['date'];

        $n = count($input['mediaoidlist']);
        $rowCount = 0;

        for ($i = 0; $i < $n; $i++) {
            $data = array(
                "missionid" => $missionid,
                "mediaoid"  => $input['mediaoidlist'][$i],
                "round"     => $round,
                "date"      => $date,
                'priority'  => 1
            );

            $code = $this->data($data)->add();
            if ($code > 0) {
                $rowCount++;
                $result['data']['missionlinkmedia'] = $this->getLastInsID();
                // 更新版本号
                $result['data']['mission'] = $this->updateMissionVersion($data['missionid']);
            } else {
                $result['error'] = "write to table missionlink media failed!";
            }
        }
        if (!isset($result['error'])) {
            $result['error'] = "success";
        }
        $result['count'] = $rowCount;

        return $result;
    }

    public function updateMissionVersion($id) {
        if (!isset($id)) {
            throw_exception(__METHOD__ . ': mission id is not set');
        }
        $Model = new Model(); // 实例化一个空模型
        // return $Model->execute('update mbis_server_mission set versionid = versionid+1 where id=' . $id);
        return $Model->execute('update '.C('DB_PREFIX').'mission set versionid = versionid+1 where id=' . $id);
    }

    /**
     * 今日已经计划播发媒体内容文件大小和
     * @return int
     */
    private function getTodayMediaSize() {
        $today = date('Y-m-d', time());
        $rows = $this->where(array('date'=>$today))->field(array('mediaoid', 'round'))->select();

        $Path = new \Content\Model\PathModel();
        $size = 0;

        foreach($rows as $row) {
            $where = array('oid' => $row['oid']);
            $t = $Path->where($where)->field('size')->select();
            $size += $t[0]['size'] * $row['round'];
        }
        return $size;
    }

    /**
     * 本期内容 => 删除
     * @param $id
     */
    public function doDelContent($id) {
        $where = array("id" => $id);
        return $this->where($where)->delete();
    }

    /**
     * 播发内容列表
     * @param $date 'YYYY-mm-dd'
     * @return mixed
     */
    public function listBroadcast($date) {
        $where = array("date" => $date);
        $fields = array('missionid', 'mediaoid as oid', 'round');

        $rows = $this->where($where)->field($fields)->select();

        $Path = new \Content\Model\PathModel();
        $n = count($rows);
        $fields_path = array('asset_name as filename', 'size');

        $Mission = new \Subscribe\Model\MissionModel();
        $Link = new \Subscribe\Model\PackagelinkmissionModel();
        $Package = new \Subscribe\Model\PackageModel();
        for ($i = 0; $i < $n; $i++) {
            // filename + size
            $oid = $rows[$i]['oid'];
            $tmp = $Path->where(array('oid' => $oid))->field($fields_path)->select();
            // Unit: Bytes => Giga Bytes
            $tmp[0]['size'] = sprintf('%.3fG', $tmp[0]['size'] / (1024 * 1024 * 1024));
            $rows[$i] = array_merge($rows[$i], $tmp[0]);

            // 业务期名
            $tmp = $Mission->where(array('id' => $rows[$i]['missionid']))->field('missionname')->select();
            $rows[$i]['missionname'] = $tmp[0]['missionname'];
            // 业务包ID
            $tmp = $Link->where(array('missionid' => $rows[$i]['missionid']))->field('packageid')->select();
            $rows[$i]['packageid'] = $tmp[0]['packageid'];
            // 业务包名
            $tmp = $Package->where(array('id' => $rows[$i]['packageid']))->field('packagename')->select();
            $rows[$i]['packagename'] = $tmp[0]['packagename'];
        }
        return $rows;
    }

    /**
     * 播发统计列表
     * @param $date
     * @return array
     */
    public function listDateStat($date) {

        // 取得当前日期yyyy-mm-dd, 前3天，后3天的日期
        $cur_ts = strtotime($date);
        $start_ts = $cur_ts - 3 * 24 * 3600;
        $end_ts = $cur_ts + 3 * 24 * 3600;

        // 7个日期数组
        $datelist = array();
        for ($ts = $start_ts; $ts < $end_ts + 1; $ts += 24 * 3600) {
            $dash = date('Y-m-d', $ts);
            array_push($datelist, $dash);
        }

        $map['date'] = array(
            array('in', $datelist),
        );
        $fields = array('missionid', 'mediaoid as oid', 'date', 'round');
        $rows = $this->where($map)->field($fields)->select();

        $Path = new \Content\Model\PathModel();
        $PM = new \Subscribe\Model\PackagelinkmissionModel();
        $Package = new \Subscribe\Model\PackageModel();
        foreach ($rows as $i => $v) {
            $oid = $v['oid'];
            $tmp = $Path->where(array('oid' => $oid))->field(array('size'))->select();
            $rows[$i]['size'] = $tmp[0]['size'];

            // packageid
            $tmp = $PM->where(array('missionid' => $v['missionid']))->field(array('packageid'))->select();
            $packageid = $tmp[0]['packageid'];
            $rows[$i]['packageid'] = $packageid;
            // packagename
            $tmp = $Package->where(array('id' => $packageid))->field(array('packagename'))->select();
            $rows[$i]['packagename'] = $tmp[0]['packagename'];
        }

        // 业务包名数组
        $packagenames = array();
        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            if (!in_array($rows[$i]['packagename'], $packagenames)) {
                array_push($packagenames, $rows[$i]['packagename']);
            }
        }

        // 初期化结果
        // array($packagename => array([yyyy-mm-dd]=>0), ...)
        $matrix = array(); // x: packagename; y: date
        foreach ($packagenames as $i => $packagename) {
            $matrix[$i] = array();
            foreach ($datelist as $ii => $date) {
                $matrix[$i][$ii] = 0;
            }
        }

        // 累加播发总量
        foreach ($rows as $i => $row) {
            $array_package = array_keys($packagenames, $row['packagename']);
            $array_date = array_keys($datelist, $row['date']);
            $matrix[$array_package[0]][$array_date[0]] += $row['size'] * $row['round'];
        }
        unset($rows);
        // 大小转换为x.xxxG
        /*
            foreach ($matrix as $key => $value) {
                $matrix[$key] = array_map(function($size) {
                    return sprintf('%.3fG', $size/1024/1024/1024);
                }, $matrix[$key]);
        */

        return array(
            'packagename' => $packagenames,
            'datelist' => $datelist,
            'matrix' => $matrix,
        );
    }

    public function listHistoryByDate($startdate, $enddate) {
        $map['date'] = array('between', $startdate . ',' . $enddate);
        $fields = array('missionid', 'mediaoid', 'round', 'date');
        $rows = $this->where($map)->field($fields)->select();

        $PackageLinkMission = new \Subscribe\Model\PackagelinkmissionModel();
        $Package = new \Subscribe\Model\PackageModel();
        $Mission = new \Subscribe\Model\MissionModel();
        $Path = new \Content\Model\PathModel();

        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            $where = array('missionid' => $rows[$i]['missionid']);
            $tmp = $PackageLinkMission->where($where)->field(array('packageid'))->select();
            // 业务包ID
            $rows[$i]['packageid'] = $tmp[0]['packageid'];
            // 业务包名
            $tmp = $Package->where(array('id' => $rows[$i]['packageid']))->field('packagename')->select();
            $rows[$i]['packagename'] = $tmp[0]['packagename'];
            // 业务期名
            $tmp = $Mission->where(array('id' => $rows[$i]['missionid']))->field('missionname')->select();
            $rows[$i]['missionname'] = $tmp[0]['missionname'];
            // 文件名 大小
            $fields = array('asset_name as filename', 'size');
            $tmp = $Path->where(array('oid' => $rows[$i]['mediaoid']))->field($fields)->select();
            $rows[$i]['filename'] = $tmp[0]['filename'];
            $rows[$i]['size'] = sprintf("%.3fG", $tmp[0]['size'] / 1024 / 1024 / 1024);
        }
        return $rows;
    }

    /**
     * 今天及以后播发的媒体内容OID ['xxx','xxx',...]
     */
    public function listOidTbd() {
        $today = date('Y-m-d', time());
        $map['date'] = array('EGT', $today);
        $rows = $this->where($map)->field('mediaoid')->order('id desc')->select();

        $res = array();
        foreach ($rows as $row) {
            $res[] = $row['mediaoid'];
        }
        return $res;
    }

    /**
     * 今天及以后播发的媒体内容 [0=>['mediaoid'=>'xxx','date'=>'yyyy-mm-dd'], 1=>[...],...]
     */
    public function listMediaTBD() {
        $today = date('Y-m-d', time());
        $map['date'] = array('EGT', $today);
        $fields = array('mediaoid', 'date', 'id');
        $rows = $this->where($map)->field($fields)->order('id desc')->select();
        return $rows;
    }

    /**
     * 查询任务(内容名称, 类型, 大小, 轮次, 播发状态)
     * @param $date
     * @return mixed
     */
    public function listTask($date) {
        $Path = new \Content\Model\PathModel();

        $fields = array('id', 'mediaoid as oid', 'round');
        $where = array('date' => $date);
        $rows = $this->where($where)->field($fields)->select();
        unset($fields);

        $Mediatype = new \Content\Model\MediatypeModel();
        $mediatype = $Mediatype->mapMediaType();

        // 播发状态 当天
        $today = date('Y-m-d', time());

        $pushStatus = new PushstatusModel();
        $fields = array('oid','roundcount', 'state', 'ratio','missionlinkmediaid');
        for ($i = 0, $n = count($rows); $i < $n; $i++) {
            // 内容名称
            $oid = $rows[$i]['oid'];
            $t = $Path->where(array('oid' => $oid))
                ->field(array('asset_name', 'mediatypeid', 'size'))
                ->select();
            $rows[$i]['asset_name'] = $t[0]['asset_name'];
            $rows[$i]['mediatypeid'] = $t[0]['mediatypeid'];
            $rows[$i]['size'] = File::humansize($t[0]['size']);
            unset($t);
            // 类型
            $rows[$i]['mediatype'] = $mediatype[$rows[$i]['mediatypeid']];

            // 已播发轮次/总轮次 播发状态

            $where['missionlinkmediaid'] = $rows[$i]['id'];
            $psRow = $pushStatus->field($fields)->where($where)->order('id desc')->select();
            $rs = &$psRow[0];

            if ($date == $today) {
                // 已播发轮次
                $rows[$i]['roundcount'] = is_null($rs['roundcount']) ? 0:$rs['roundcount'];

                $st = intval($rs['state']);
                switch ($st) {
                    case 0:
                        // 播发进度100% => 播发完成
                        if ($rs[0]['ratio'] == 10000) {
                            $rows[$i]['status'] = '播发完成';
                            $rows[$i]['roundcount'] = $rows[$i]['round'];
                        } else {
                            $rows[$i]['status'] = '未播发';
                        }
                        break;
                    case 1:
                        $rows[$i]['status'] = '正在播发';
                        if ($rows[$i]['roundcount'] < $rows[$i]['count']) {
                            $rows[$i]['roundcount'] += 1;
                        }
                        break;
                    case 2:
                        $rows[$i]['status'] = '播发完成';
                        $rows[$i]['roundcount'] = $rows[$i]['round'];
                        break;
                    default:
                        $rows[$i]['status'] = '未播发';
                }
            } else if ($date < $today) {
                $rows[$i]['status'] = '已播发';
                $rows[$i]['roundcount'] = $rows[$i]['round'];
            } else {
                $rows[$i]['status'] = '未播发';
                $rows[$i]['roundcount'] = 0;
            }
        }
        unset($pushStatus);
        return $rows;
    }
}