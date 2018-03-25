<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-28
 * Time: 13:46
 */
namespace Pushctrl\Model;
use Think\Model;

class QueryModel extends Model {

    /**
     * 查询任务(内容名称, 类型, 大小, 轮次, 播发状态)
     * @param $date
     * @return mixed
     */
    public function listTask($date) {
        $Path = M('path');
        $Link = M('missionlinkmedia');

        $fields = array('id', 'mediaoid as oid', 'round');
        // $where = array('state' => 1);
        $where = array('date' => $date);
        $rows = $Link->where($where)->field($fields)->select();

        $model = new \Pushctrl\Model\IndexModel();
        $mediatype = $model->mapMediaType();

        // 播发状态
        $today = date('Y-m-d', time());
        if ($date < $today) {
            $status = '已播发';
        } else if ($date == $today) {
            $status = '今日播发';
        } else {
            $status = '未播发';
        }

        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            // 内容名称
            $t = $Path->where(array('oid' => $rows[$i]['oid']))->field(array('asset_name', 'mediatypeid', 'size'))->select();
            $rows[$i]['asset_name'] = $t[0]['asset_name'];
            $rows[$i]['mediatypeid'] = $t[0]['mediatypeid'];
            $rows[$i]['size'] = humansize($t[0]['size']);
            // 类型
            $rows[$i]['mediatype'] = $mediatype[$rows[$i]['mediatypeid']];
            // 播发状态
            $rows[$i]['status'] = $status;
        }
        return $rows;
    }
}