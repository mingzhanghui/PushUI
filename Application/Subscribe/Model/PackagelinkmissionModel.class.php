<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-26
 * Time: 15:18
 */
namespace Subscribe\Model;
use Think\Model;

class PackagelinkmissionModel extends Model {

    protected $tableName = 'packagelinkmission';
    protected $fields = array('id', 'packageid', 'missionid');
    protected $pk = 'id';

    /**
     * 查找一个业务包下的业务期个数
     * @param $packageid
     * @return mixed
     */
    public function countMission($packageid) {
        $where = array('packageid' => $packageid);
        return $this->where($where)->count();
    }

    /**
     * table: MBIS_Server_PackagelinkMission (业务包和期关联表)
     * @param $pkgid
     * @return array
     */
    public function getMissionIdByPackageId($pkgid) {
        $where = array("packageid" => $pkgid);
        $array = $this->field("missionid")->where($where)->order('id desc')->select();
        $n = count($array);
        $list = array();
        for ($i = 0; $i < $n; $i++) {
            array_push($list, $array[$i]['missionid']);
        }
        unset($array);
        return $list;
    }
}