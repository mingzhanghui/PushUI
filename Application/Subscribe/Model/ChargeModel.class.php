<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-19
 * Time: 09:17
 */
namespace Subscribe\Model;
use Think\Model;

class ChargeModel extends Model {

    // 单个媒体文件 播发任务 列表
    public function listPackageTask($oid) {
        $where = array('mediaoid'=>$oid);
        // 1. 取得媒体 的业务期ID 和播发日期
        $fields = array('missionid', 'date', 'round');
        $rows = M("missionlinkmedia")->where($where)->field($fields)->select();

        $Mission = M('mission');
        $Link = M('packagelinkmission');
        $Package = M('package');
        foreach ($rows as $i => $value) {
            $missionid = $value['missionid'];
            // 2. 业务期名
            $tmp = $Mission->where(array('id'=>$missionid))->field(array('missionname'))->select();
            $rows[$i]['missionname'] = $tmp[0]['missionname'];
            // 3. 业务包ID
            $tmp = $Link->where(array('missionid'=>$missionid))->field(array('packageid'))->select();
            $packageid = $tmp[0]['packageid'];
            $rows[$i]['packageid'] = $packageid;
            // 4. 业务包名
            $tmp = $Package->where(array('id'=>$packageid))->field(array('packagename'))->select();
            $rows[$i]['packagename'] = $tmp[0]['packagename'];
        }
        return $rows;
    }

    // 多个媒体文件 播发任务 列表
    public function listMultiPackageTask($oidstring) {
        // 1. 取得媒体 的业务期ID 和播发日期
        $tmp = array();
        if (is_string($oidstring)) {
            $str = "'" . preg_replace('/,/', "','", $oidstring) . "'";
            $where = 'where mediaoid in (' . $str . ')';
            $sql = 'SELECT missionid, date, round FROM MBIS_Server_MissionlinkMedia ' . $where;
            $tmp = M()->query($sql);
        } else if (is_array($oidstring)) {
            $map['mediaoid'] = array('IN', $oidstring);
            $fields = array('missionid', 'date', 'round');
            $tmp = M('missionlinkmedia')->where($map)->field($fields)->select();
        }

        $rows = array();   // => result
        $missionids = array();  // distinct missionid
        foreach ($tmp as $i => $value) {
            $missionid = $value['missionid'];
            if (!in_array($missionid, $missionids)) {
                array_push($missionids, $missionid);
                array_push($rows, $value);
            }
        }
        unset($missionids);

        $Mission = M('mission');
        $Link = M('packagelinkmission');
        $Package = M('package');

        foreach ($rows as $i => $value) {
            $missionid = $value['missionid'];

            // 2. 业务期名
            $tmp = $Mission->where(array('id'=>$missionid))->field(array('missionname'))->select();
            $rows[$i]['missionname'] = $tmp[0]['missionname'];
            // 3. 业务包ID
            $tmp = $Link->where(array('missionid'=>$missionid))->field(array('packageid'))->select();
            $packageid = $tmp[0]['packageid'];
            $rows[$i]['packageid'] = $packageid;
            // 4. 业务包名
            $tmp = $Package->where(array('id'=>$packageid))->field(array('packagename'))->select();
            $rows[$i]['packagename'] = $tmp[0]['packagename'];
        }

        return $rows;
    }


}