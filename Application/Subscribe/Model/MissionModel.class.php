<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-14
 * Time: 09:36
 */
namespace Subscribe\Model;
use Content\Model\MediaModel;
use Think\Model;

class MissionModel extends Model {
    protected $tableName = 'mission';
    protected $fields = array('id','missionname','missiondescription','startdate', 'enddate', 'state', 'versionid', 'synversionid');
    protected $pk = 'id';

    protected $_validate = array(
        array('missionname','require','业务期名必须！'),   //默认情况下用正则进行验证
        // array('packagename','','业务期名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
    );
    /**
     * Mission表（业务期描述表）
     * 业务中的期的描述。在查询管理期号页面中管理。
     * @param $id
     */
    public function getMissionInfoById($id) {
        $fields = array(
            "id as mid",
            "missionname",
            "missiondescription",
            "startdate",
            "enddate",
//            "state",
//            "versionid",
            "synversionid"
        );
        $where = array("ID" => $id);
        $array = M('mission')->field($fields)->where($where)->select();
        return $array[0];
    }

    /**
     * 本期内容详情 修改
     * @param $data
     * @return bool
     */
    public function editMission($data) {
        if (!isset($data['id'])) {
            throw_exception(__FUNCTION__ . " mission id is not set");
        }
        $where = array("id" => $data['id']);
        unset($data['id']);
        $res['mission'] = M("mission")->data($data)->where($where)->save();
        return $res;
    }

    /**
     * 删除业务期  @mid: MissionID
     */
    public function delMission($mid) {
        // 业务期下是否有媒体内容
        $where = array("MissionID" => $mid);
        $count = M("missionlinkmedia")->where($where)->count();
        if (0 < $count) {
            return array(
                "code"    => -1,
                "msg"     =>"业务期下有内容, 请先删除内容",
            );
        }
        $tables = array('packagelinkmission', 'missionused');
        $res = array();
        foreach ($tables as $table) {
            $res[$table] = M($table)->where($where)->delete();
        }
        $where = array("ID"  => $mid);
        $res['mission'] = M("mission")->where($where)->delete();

        return array(
            "code" => 0,
            "msg"  => "业务期删除成功",
            "res"  => $res
        );
    }

    /**
     * @param $date string "yyyy-mm-dd"
     * @return bool 指定した日付が有効な場合に TRUE、そうでない場合に FALSE を返します。
     */
    public function mycheckdate($date) {
        $a = explode("-", $date);
        if ( 3 == count($a) ) {
            $month = $a[1];
            $day = $a[2];
            $year = $a[0];
            return checkdate($month, $day, $year);
        }
        return false;
    }

    /**
     * 修改业务期日期 检查日期合法
     * @param $a array("pkgid"=>xx, "startdate"=>xx, "enddate"=>xx)
     * return: array['code'] <0 NG, ==0 OK
     * array('code'=>xx, 'msg'=>xx)
     *  -1  开始日期或结束日期为空
     *  -2  开始日期格式错误yyyy-mm-dd(e.g. 2017-04-14)
     *  -3  结束日期格式错误yyyy-mm-dd(e.g. 2017-04-14)
     *  -4  开始日期不能大于结束日期
     *  -5  开始日期在已有日期范围内
     *  -6  结束日期在已有日期范围内
     *  -7  开始日期或结束日期不合法
     */
    public function checkPackageDateRange($a) {
        if (!isset($a['packageid'])) {
            throw_exception(__METHOD__ . " packageid is not set!");
        }
        if (!isset($a['id'])) {
            throw_exception(__METHOD__ . " missionid (id) is not set!");
        }
        if (!array_key_exists('startdate', $a) || !array_key_exists('enddate', $a)) {
            $res['code'] = -1;
            $res['msg'] = "开始日期或结束日期为空";
            return $res;
        }
        $packageid = $a['packageid'];
        $startdate = $a['startdate'];
        $enddate = $a['enddate'];
        $missionid = $a['id'];

        if (!$this->mycheckdate($startdate)) {
            return array('code'=>-2, 'msg'=>'开始日期格式错误yyyy-mm-dd(e.g. 2017-04-14)');
        }
        if (!$this->mycheckdate($enddate)) {
            return array('code'=>-3, 'msg'=>'结束日期格式错误yyyy-mm-dd(e.g. 2017-04-14)');
        }
        if ($startdate > $enddate) {
            return array('code'=>-4, 'msg'=>'开始日期不能小于结束日期');
        }

        $where = array('packageid' => $packageid);
        $fields = array('missionid');
        $missionidlist = M("packagelinkmission")->where($where)->field($fields)->select();

        $rangelist = array();
        $Mission = M("mission");
        $fields = array("id", "startdate", "enddate");
        foreach ($missionidlist as $value) {
            $where = array("id" => $value['missionid']);
            $rows = $Mission->where($where)->field($fields)->select();
            array_push($rangelist, $rows[0]);
        }
        unset($missionidlist);
        // dump($rangelist);

        // @rangelist: array(
        //    0 => array('id'=>1, 'startdate'=>'2017-03-02', 'enddate'=>'2017-03-06'),
        //    1 => array('id'=>2, 'startdate'=>'2017-04-01', 'enddate'=>'2017-04-02'),
        // ...);
        foreach($rangelist as $range) {
            if ($missionid == $range['id']) {
                continue;   // 要修改的业务期 不比较自己
            }
            $rs = $range['startdate'];
            $re = $range['enddate'];
            if ($rs <= $startdate && $startdate <= $re) {
                return array('code'=>-5, 'msg'=>'开始日期在已有日期范围内');
            }
            if ($rs <= $enddate && $enddate <= $re) {
                return array('code'=>-6, 'msg'=>'结束日期在已有日期范围内');
            }
            if (($startdate<=$rs && $enddate>=$rs) || ($startdate<=$re && $enddate>=$re)) {
                return array('code'=>-7, 'msg'=>'开始日期或结束日期不合法');
            }
        }
        unset($rangelist);

        return array('code'=>0, 'msg'=>'日期合法');
    }

    public function listMediaTypeID() {
        $where = "id != 0 AND mediatype not like '%总集'";
        $res = array();
        $rows = M('mediatype')->where($where)->field(array('id'))->select();
        foreach ($rows as $row) {
            array_push($res, $row['id']);
        }
        unset($rows);
        return $res;
    }

    // 按文件名和类型查找 已经提交并审核过的媒体文件 e.g.电视节目
    public function listMissionContent($mediatypeid, $asset_name = '') {
        $list = array();

        $config = \Common\Common\Config::getInstance();
        if ($config->hasReviewed()) {
            $where = array(
                'editstatus'  => array('GT', 2),    // 已经审核 3
                'slicestatus' => array('NEQ', 0),   // 已经备播
            );
        } else {
            $where = array(
                'editstatus'  => array('EGT', 2),   // 已经提交 2,3
                'slicestatus' => array('NEQ', 0),   // 已经备播
            );
        }
        // $res = M('editstatus')->where($where)->select();
        // $fields = array('OID');
        $rows = M('editstatus')->where($where)->field('oid')->select();
        unset($where);

        $Path = M("path");
        $Media = new MediaModel();
        $fields = array("oid", "mediatypeid", "asset_name", "size");
        if ('' == $asset_name) {
            foreach ($rows as $row) {
                $where = array("oid" => $row['oid']);
                $res = $Path->where($where)->field($fields)->select();
                if ($res[0]['mediatypeid'] == $mediatypeid) {
                    $res[0]['size'] = humansize($res[0]['size']);
                    $t = $Media->where($where)->field('title')->select();
                    if ($t[0]) {
                        $res[0]['asset_name'] = $t[0]['title'];
                    }
                    array_push($list, $res[0]);
                }
            }
        } else {
            $asset_name = trim( $asset_name );
            $pat = sprintf("/%s/", $asset_name);
            foreach ($rows as $row) {
                $where = array("oid" => $row['oid']);
                $res = $Path->where($where)->field($fields)->select();
                if ($res[0]['mediatypeid'] != $mediatypeid)
                    continue;
                $matches = array();
                preg_match($pat, $res[0]['asset_name'], $matches);
                if (count($matches) > 0) {
                    $res[0]['size'] = humansize($res[0]['size']);
                    $t = $Media->where($where)->field('title')->select();
                    if ($t[0]) {
                        $res[0]['asset_name'] = $t[0]['title'];
                    }
                    array_push($list, $res[0]);
                }
            }
        }
        unset($rows);

        return $list;
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

            $Link = M("missionlinkmedia");
            $code = $Link->data($data)->add();
            if ($code > 0) {
                $rowCount++;
                $result['data']['missionlinkmedia'] = $Link->getLastInsID();
                // 更新版本号
                $result['data']['mission'] = $this->updateMissionVersion($data['missionid']);
            } else {
                $result['error'] = "write to table missionlink media failed!";
            }
        }
        if (!isset($result['error'])) {
            $result['error'] = "success";
        }

        return $result;
    }

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
        $range = $this->getMissionDateRange($data['missionid']);
        $date = strtotime($data['date']);   // to check
        $start = strtotime($range['startdate']);
        $end = strtotime($range['enddate']);

        if ($date < $start || $date > $end) {
            $result['code'] = -1;
            $result['error'] = "播发日期不在业务期日期范围内!";
            return $result;
        }

        $missionid = $data['missionid'];
        $date = $data['date'];

        $sizesum = 0;
        $Path = M('Path');
        foreach ($data['mediaoidlist'] as $mediaoid) {
            $where = array(
                'missionid' => $missionid,
                'mediaoid'  => $mediaoid,
                'date'      => $date
            );
            $count = M("missionlinkmedia")->where($where)->count();
            if ($count > 0) {
                $result['code'] = -2;
                $result['error'] = "已经存在相同的播发计划: missionid=".$missionid ;
                return $result;
            }
            $t = $Path->where(array('oid'=>$mediaoid))->field('size')->select();
            $sizesum += $t[0]['size'];
        }
        // 今日既存播发总量
        $sizesum += $this->getTodayMediaSize();

        // check 播发总量 error
        $sizesum /= 1024 * 1024 * 1024; // G
        $model = new \Subscribe\Model\BroadcastModel();
        $quota = $model->getQuota();
        if ($quota < $sizesum) {
            $result['code'] = -3;
            $result['error'] = '播发计划已经超出当日播发总量';
            return $result;
        }

        $result['data'] = true;
        $result['error'] = 'OK';

        return $result;
    }

    private function getTodayMediaSize() {
        $Link = M('missionlinkmedia');
        $today = date('Y-m-d', time());
        $rows = $Link->where(array('date'=>$today))->field('mediaoid')->select();

        $Path = M('path');
        $size = 0;
        foreach($rows as $row) {
            $where = array('oid' => $row['oid']);
            $t = $Path->where($where)->field('size')->select();
            $size += $t[0]['size'];
        }
        return $size;
    }

    public function getMissionDateRange($id) {
        if (!isset($id)) {
            throw_exception(__METHOD__ . ": mission id...");
        }
        $fields = array('startdate', 'enddate');
        $rows = M("mission")->where(array("id"=>$id))->field($fields)->select();
        return $rows[0];
    }

    public function updateMissionVersion($id) {
        if (!isset($id)) {
            throw_exception(__METHOD__ . ': mission id is not set');
        }
        $Model = new Model(); // 实例化一个空模型
        // return $Model->execute('update mbis_server_mission set versionid = versionid+1 where id=' . $id);
        return $Model->execute('update '.C('DB_PREFIX').'mission set versionid = versionid+1 where id=' . $id);
    }

    public function checkEditContentDate($missionid, $date) {
        $fields = array("startdate", "enddate");
        $where = array("id" => $missionid);
        $rows = M("mission")->field($fields)->where($where)->select();
        $start = $rows[0]['startdate'];
        $end = $rows[0]['enddate'];
        if ($date < $start || $date > $end) {
            return false;
        }
        return true;
    }

    /**
     * 增加业务期版本号
     */
    public function versionInc($id) {
        $where = array('id' => $id);
        return $this->where($where)->setInc('versionid', 1);
    }

}