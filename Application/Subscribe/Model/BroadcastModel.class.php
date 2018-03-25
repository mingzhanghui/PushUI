<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-04-21
 * Time: 17:41
 */
namespace Subscribe\Model;
use Content\Model\MediaModel;
use Think\Model;

class BroadcastModel extends Model {

	// 播发内容列表
	public function listBroadcast($date) {
		$where = array("date" => $date);
		$fields = array('missionid', 'mediaoid as oid', 'round');

		$rows = M('missionlinkmedia')->where($where)->field($fields)->select();

		$Path = M('Path');
		$n = count($rows);
		$fields_path = array('asset_name as filename', 'size');
        $Media = new MediaModel();

		$Mission = M('mission');
		$Link = M('packagelinkmission');
		$Package = M('package');
		for ($i = 0; $i < $n; $i++) {
			// filename + size
			$oid = $rows[$i]['oid'];
			$tmp = $Path->where(array('oid' => $oid))->field($fields_path)->select();
            $m = $Media->where(array('oid' => $oid))->field('title')->select();
            if (count($m) > 0) {
                $tmp[0]['title'] = $m[0]['title'];
            } else {
                $tmp[0]['title'] = $tmp[0]['filename'];
            }
			// Unit: Bytes => Giga Bytes
			$tmp[0]['size'] = sprintf('%.3fG', $tmp[0]['size'] / (1024 * 1024 * 1024));
			$rows[$i] = array_merge($rows[$i], $tmp[0]);

			// 业务期名
			$tmp = $Mission->where(array('id' => $rows[$i]['missionid']))->field(array('missionname'))->select();
			$rows[$i]['missionname'] = $tmp[0]['missionname'];
			// 业务包ID
			$tmp = $Link->where(array('missionid' => $rows[$i]['missionid']))->field(array('packageid'))->select();
			$rows[$i]['packageid'] = $tmp[0]['packageid'];
			// 业务包名
			$tmp = $Package->where(array('id' => $rows[$i]['packageid']))->field(array('packagename'))->select();
			$rows[$i]['packagename'] = $tmp[0]['packagename'];
		}
		return $rows;
	}

	// date cursor  <= x, x, x, [cur], x, x, x, =>
	public function dateList(&$ul, &$moveLeft, &$moveRight, $date) {
		$cur_ts = strtotime($date);
		$start_ts = $cur_ts - 3 * 24 * 3600;
		$end_ts = $cur_ts + 3 * 24 * 3600 + 1;

		$url = U();
		$ul = '<ul class="uldate">';
		for ($ts = $start_ts; $ts < $end_ts; $ts += 24 * 3600) {
			$slash = date('Y/m/d', $ts);
			$dash = date('Y-m-d', $ts);
			if ($ts == $cur_ts) {
				$ul .= "<li class=\"lidate cur\"><a href=\"" . $url . "?date=" . $dash . "\">" . $slash . "</a></li>";
			} else {
				$ul .= "<li class=\"lidate\"><a href=\"" . $url . "?date=" . $dash . "\">" . $slash . "</a></li>";
			}
		}
		$ul .= '</ul>';

		// move left
		$prev = date('Y-m-d', $cur_ts - 24 * 3600);
		$moveLeft = "<a href='" . $url . '?date=' . $prev . "' title='后退一天'><div class=\"arrow move-left\"></div></a>";

		// move right
		$next = date('Y-m-d', $cur_ts + 24 * 3600);
		$moveRight = "<a href='" . $url . '?date=' . $next . "' title='前进一天'><div class=\"arrow move-right\"></div></a>";
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
		$fields = array('missionid', 'mediaoid as oid', 'date');
		$rows = M('missionlinkmedia')->where($map)->field($fields)->select();

		$Path = M('path');
		$PM = M('packagelinkmission');
		$Package = M('package');
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

		// 初始化结果
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
			$matrix[$array_package[0]][$array_date[0]] += $row['size'];
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
		$rows = M('missionlinkmedia')->where($map)->field($fields)->select();

		$PackageLinkMission = M('packagelinkmission');
		$Package = M('package');
		$Mission = M('mission');
		$Path = M('path');

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
     * 播发总量
     * @return mixed
     */
	public function getQuota() {
        $where = array('name' => 'totalsource');
        $Configure = M("configure");
        $t = $Configure->where($where)->field('intvalue')->select();

        return $t[0]['intvalue'];
    }

}