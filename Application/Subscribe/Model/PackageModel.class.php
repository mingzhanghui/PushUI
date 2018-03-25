<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-27
 * Time: 15:28
 */
namespace Subscribe\Model;
use Home\Model\ConfigureModel;
use Think\Model;

class PackageModel extends Model {

    protected $tableName = 'package';
    protected $tablePrefix = 'MBIS_Server_';

    protected $fields = array(
        'id', 'packageoid', 'packagename', 'packagedescription', 'thumb', 'updatecycletypeid', 'price',
        'starttime', 'originaldir', 'parentid', 'isnode', 'packagetypeid', 'packagetemplateid',
        'chargetype', 'qrcode'
    );
    protected $pk = 'id';

    // 业务包编辑 业务包列表tree
    public function getPackageList() {
        $model = new PackageModel();
        $result = $model->field(array(
            0 => 'id',
            1 => 'packagename',
            2 => 'parentid',
            3 => 'isnode'
        ))->select();

        $tree = $this->buildTree($result);

        return $tree;
    }

    /**
     * @param $items
     * $items = array(
     *            array('id' => 42, 'parentid' => 1),
     *            array('id' => 43, 'parentid' => 42),
     *            array('id' => 1,  'parentid' => 0));
     * @return mixed
     * Array (
     *   [0] => Array(
     *     [id] => 1
     *     [parentid] => 0
     *     [childs]   => Array(
     *       [0] => Array (
     *          [id] => 42
     *          [parentid] => 1
     *          [childs] => Array(
     *             [0] => Array(
     *                [id] => 43
     *                [parentid] => 42
     *             )
     *          )
     *       )
     *     )
     *   )
     * )
     */
    private function buildTree($items) {
        $childs = array();

        foreach($items as &$item) {
            $childs[$item['parentid']][] = &$item;
            unset($item);
        }
        foreach($items as &$item) {
            if (isset($childs[$item['id']])) {
                $item['childs'] = $childs[$item['id']];
            }
            unset($item);
        }
        return $childs[0];
    }

    /**
     * 计费周期定义，供下拉列表用，在系统设置页面中的业务包设置中管理
     * @return mixed
     */
    public function getChargeTypeList() {
        $model = M('chargetype');
        $where['chargetypeid'] = array('neq', 0);

        return $model->field(array(
            'chargetypeid',
            'chargetypename'
        ))->where($where)->select();
    }

    // packgetemplate 业务包模板描述表
    public function getpackagetpllist() {
        $model = M('packagetemplate');
        $where['PackageTemplateID'] = array('gt', 0);
        return $model->field(array(
            'ID',
            'PackageTemplateID',
            'Description'
        ))->where($where)->order('id asc')->select();
    }

    // @packagetype: 业务包类型
    public function getpackagetypelist() {
        $model = M('packagetype');
        $where['PackageTypeID'] = array('neq', 0);
        return $model->field(array(
            'ID',
            'PackageTypeID',
            'Description'
        ))->where($where)->select();
    }

    /**
     * 添加新的业务包 @table: MBIS_Server_Package
     * @param $data
     * @return mixed
     */
    public function newPackage($data) {
        $parentid = $data['parentid'];
        // 父节点设为不是节点
        if (0 != $parentid) {
            $this->where( array('id'=>$parentid) )->data( array('isnode'=>0) )->save();
        }
        return $this->data($data)->add();
    }

    /**
     * 取得下一级子业务包
     * @param $id
     * @return array
     */
    public function getChildIdList($id) {
        if (!isset($id)) {
            throw_exception(__FUNCTION__ . ": package id is undefined!");
        }
        $where = array("parentid" => $id);
        $res = $this->field("id")->where($where)->select();
        $n = count($res);
        if (0<$n) {
            $list = array();
            for ($i = 0; $i < $n; $i++) {
                array_push($list, $res[$i]['id']);
            }
            unset($res);
            return $list;
        }
        return null;
    }

    /**
     * 递归取得所有子业务包
     * @param $list
     * @param $id
     * @return int
     */
    public function getChildIdListR(&$list, $id) {
        if (!isset($id)) {
            throw_exception(__FUNCTION__ . ": package id is undefined!");
        }
        $childs = $this->getChildIdList($id);
        if (0 == count($childs)) {
            return 0;
        }
        $list = array_merge($list, $childs);

        foreach ($childs as $item) {
            $this->getChildIdListR($list, $item);
        }
        return 0;
    }

    /**
     * table: MBIS_Server_PackagelinkMission (业务包和期关联表)
     * @param $pkgid
     * @return array
     */
    public function getMissionIdByPackageId($pkgid) {
        $model = M('packagelinkmission');
        $where = array("packageid" => $pkgid);
        $array = $model->field(array("missionid"))->where($where)->order('id desc')->select();
        $n = count($array);
        $list = array();
        for ($i = 0; $i < $n; $i++) {
            array_push($list, $array[$i]['missionid']);
        }
        unset($array);
        return $list;
    }

    public function listNodePackages() {
        $where = array("isnode" => 1);
        $fields = array("id", "packagename");
        return $this->where($where)->field($fields)->select();
    }

    /**
     * 新建业务期
     * @param $data
     */
    public function addMission($data) {
        $modelMission = M("mission");

        $PackageID = $data['PackageID'];
        unset($data['PackageID']);
        $data['State'] = 0;
        $data['VersionID'] = 1;
        $data['SynVersionID'] = 0;

        $modelMission->data($data)->add();
        $MissionID = $modelMission->getLastInsID();

        $linkid = 0;
        if (1 <= $MissionID) {
            unset($data);
            $data = array(
                'PackageID' => $PackageID,
                'MissionID' => $MissionID
            );
            $modelLink = M("packagelinkmission");
            $modelLink->data($data)->add();
            // transaction
            $linkid = $modelLink->getLastInsID();
            if ($linkid < 1) {
                $modelMission->where(array("ID" => $MissionID))->delete();
                return array('packageid'=> $PackageID,
                    'missionid'=> $MissionID,
                    'linkid' => -255);
            }
        }
        return array(
            'packageid' => $PackageID,
            'missionid'=> $MissionID,
            'linkid' => $linkid
        );
    }


    // /PushUI/resource/appendix/
    public function getPackageURLPrefix() {
        $where = array("name" => "package_path");
        $fields = array("stringvalue");
        $rows = M("configure")->where($where)->field($fields)->select();
        $package_path = $rows[0]['stringvalue'];
        $package_path = rtrim($package_path, "\\/");
        $root = rtrim($_SERVER['DOCUMENT_ROOT'], "\\/") . "/";
        $len = strlen($root);
        return substr($package_path, $len - 1);
    }

    /**
     * 获取业务包的层次
     */
    public function getPackageDepth($id) {
        $depth = 0;
        $parentid = -1;
        $where = array('id' => $id);

        while ($parentid) {
            $rows = $this->field('parentid')->where($where)->select();
            if (count($rows) == 0) {
                break;
            }
            $parentid = $rows[0]['parentid'];
            $depth += 1;
            $where['id'] = $parentid;
        }
        return $depth;
    }

    /**
     * 删除一个业务包
     * @param $id
     * @return int|mixed
     */
    public function delOnePackage($id) {
        $where = array("packageid" => $id);
        $count = M("packagelinkmission")->where($where)->count();
        if ($count > 0) {
            return -1;  // 业务包下有业务期, 请先删除业务期
        }

        // 删除缩略图 原图 二维码图
        $configModel = new \Home\Model\ConfigureModel();
        $packageDir = $configModel->getconfpathbyname('package_path');
        $packageDir = rtrim($packageDir, "/") . "/";

        $myrmdir = function($dir) {
            if (is_dir($dir)) {
                \Content\Common\File::emptydir($dir);
                rmdir($dir);
            }
        };
        $dir = $packageDir.'package'.$id;
        $myrmdir($dir);

        $dir = $packageDir.'package'.$id.'thumb';
        $myrmdir($dir);

        return $this->where(array('id' => $id))->delete();
    }

    /**
     * 保存业务包图片到本地
     */
    public function saveQrCode($id, $packagetypeid) {

        $config = new ConfigureModel();
        $dir = $config->getconfpathbyname('package_path');
        unset($config);

        $dir = rtrim($dir, '\\/') . '/package'.$id;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        $filename = $id.'_qrcode.png';
        $path = $dir .'/'.$filename;
        $rel = 'package'.$id.'/'.$filename;

        $packagetypeid = intval($packagetypeid);
        $type = '';
        switch ($packagetypeid) {
            case 1:
                $type = 'scan';
                break;
            case 2:
                $type = 'theaters';
                break;
            case 3:
                $type = 'push';
                break;
            default:

        }

        $uri = '/program/push?typeid='.$id;
        // $url = C("QR_URL") . $uri;
        $url = $this->qrURL($uri);
        include './ThinkPHP/Extend/Vendor/phpqrcode/phpqrcode.php';
        $errorLevel = 3;  // 容错级别
        $matrixPointSize = 1;       // 生成图片大小
        $margin = 2;
        // 生成二维码图片
        $object = new \QRcode();

        $object->png($url, $path, $errorLevel, $matrixPointSize, $margin);
        $data['qrcode'] = $rel;
        $this->data($data)->where(array('id'=>$id))->save();
        $object->png( $url, $path, $errorLevel, $matrixPointSize, $margin );

        return $rel;
    }

    /**
     * 生成业务包OID
     */
    public function genPackageOID() {
        $id = $this->max('id');
        $oid = 'P' . date('YmdHis', time());   // len 1 + 14 + 17 = 32
        return $oid.str_pad($id, 17, '0', STR_PAD_LEFT);
    }

    /**
     * 拼接后的实际url
     * @param $uri string 内容oid uri
     * @return string
     */
    public function qrURL($uri) {
        $url = C('QR_URL') .'&redirect_uri='.C('QR_REDIRECT') .$uri . C('QR_SUFFIX');
        return $url;
    }

}