<?php
namespace Subscribe\Controller;
use Subscribe\Model\PackageModel;
use Think\Controller;

use Common\Common\Config;
use Common\Common\Logger;

// 业务管理首页 (业务编辑)
class IndexController extends Controller {
    public function __construct() {
        parent::__construct();

        if (!isset($_SESSION)) {
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['subscribe']) {
                redirect( U('Subscribe/Login/index').'?refer=' . urlencode(U()) );
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['subscribe'] = 1;
        }
    }

    public function index() {

        $package = new \Subscribe\Model\PackageModel();
        // 计费周期
        $chargeTypeList = $package->getChargeTypeList();
        $this->assign('chargeTypeList', $chargeTypeList);
        // 业务包模板
        $packagetpllist = $package->getpackagetpllist();
        $this->assign('packagetpllist', $packagetpllist);
        // 业务包类型
        $packagetypelist = $package->getpackagetypelist();
        $this->assign('packagetypelist', $packagetypelist);

        // 订阅周期
        $fields = array('id', 'updatecycletype', 'updatecycledescription');
        $updateCycleTypeList = M('updatecycletype')->field($fields)->order('id desc')->select();
        $this->assign('updateCycleTypeList', $updateCycleTypeList);

        // cookie __ROOT__ /PushUI
        setcookie('mbis_root', __ROOT__, time()+300);

        $this->display();
    }

    public function getPackageList() {
        $package = new \Subscribe\Model\PackageModel();
        $this->ajaxReturn( $package->getPackageList() );
    }

    /**
     * 取得业务包
     */
    public function getPackageInfo() {
        $id = I('get.id');
        if (!isset($id)) {
            throw_exception("package id is not set");
        }
        $package = new \Subscribe\Model\PackageModel();
        $data = $package->find($id);
        $data['price'] = sprintf("%.2f", $data['price']/100);
        $this->ajaxReturn( $data );
    }

    /**
     * 新建业务包
     */
    public function newPackage() {
        $packagetypeid = I('post.PackageTypeID');

        // 1. 存储业务基本信息
        $data = array(
            'packagename'        => I('post.PackageName'),
            'packagedescription' => I('post.PackageDescription'),
            'price'              => 100 * I('post.Price'),
            'updatecycletypeID'  => I('post.UpdateCycleTypeID'),   // 订阅周期
            'isnode'             => 1,                             // 新建业务包是叶子节点
            'packagetypeid'      => $packagetypeid,       // 业务包类型
            'packagetemplateid'  => I('post.PackageTemplateID'),   // 业务包模板
            'chargetypeid'       => I('post.ChargeTypeID')         // 计费周期
        );
        $data['parentid'] = intval($_POST['sel_pid']);

        $pic = $_FILES['pic'];
        $filename = $pic['name'];
        // 中文... WTF
        if (!preg_match('/^[A-Za-z0-9\. \ ]+$/', $filename)) {
            $a = explode('/', $pic['type']);
            $ext = array_pop($a);
            $filename = date('YmdHis', time()).str_pad(rand(0,999), 3).'.'.$ext;
        }
        $_FILES['pic']['name'] = $filename;

        $model = new \Subscribe\Model\PackageModel();
        // package oid
        $oid = $model->genPackageOID();
        $data['packageoid'] = $oid;

        $lastinsid = $model->newPackage( $data );
        unset($data);
        $where = array(
            'id' => $lastinsid,
        );
        // 二维码图片
        $data['qrcode'] = $model->saveQrCode($lastinsid, $packagetypeid);
        $model->data($data)->where($where)->save();

        // 2. 上传业务包图片
        $confModel = new \Home\Model\ConfigureModel();
        $packagePath = $confModel->getpathbyname("package_path");
        // D:/software/wamp/www/PushUI/resource/package/
        $packagePath = rtrim($packagePath, "\\/") . "/";

        // D:\software\wamp\www\PushUI\resource\package\package17\17.jpg
        $getSaveName = function($name, $id) {
            // remove ext
            $parts = explode(".", $name);
            array_pop($parts);
            $name = implode(".", $parts);
            return sprintf("%d_%s", $id, $name);
        };
        $getSubName = function($id) {
            return "package" . $id;
        };
        $config = array(
            'maxSize'    => 3145728,
            'rootPath'   => $packagePath,
            'savePath'   => '',
            'saveName'   => array($getSaveName, array('__FILE__', $lastinsid)),
            // 1个业务包1个图片
            'replace'    => true,
            'exts'       => array('jpg', 'gif', 'png', 'jpeg'),

            // 开启子目录保存 并以packageID（格式为package + [ID], eg: package65）为子目录
            'autoSub'    =>  true,
            'subName'    => array($getSubName, array($lastinsid)),
        );
        $upload = new \Think\Upload($config);// 实例化上传类

        $info = $upload->upload();
        if(!$info) {// 上传错误返回错误信息
            $this->error($info);
        }
        $OriginalDir = $packagePath . $info['pic']['savepath'] . $info['pic']['savename'];
        // package26thumb/12yearaslave.jpg
        $ThumbRelDir = "package" . $lastinsid . "thumb/";
        // @$Thumb 相对于 __ROOT__/resource/package的路径
        $Thumb = $ThumbRelDir. $info['pic']['savename'];
        $ThumbAbsDir = $packagePath . $ThumbRelDir;
        $this->mymkdir($ThumbAbsDir);

        // 业务包缩略图生成
        $image = new \Think\Image();
        $image->open($OriginalDir);

        $width = $confModel->getconfintbyname('thumb_width');
        $height = $confModel->getconfintbyname('thumb_height');

        $ThumbPath = $packagePath . $Thumb;
        // 按照原图的比例生成一个最大为$widthx$height并保存为$ThumbPath
        $image->thumb($width, $height)->save($ThumbPath);

        // 3. 存储数据库 业务包图片和缩略图
        $data = array(
            'originaldir' => $OriginalDir,
            'thumb'       => $Thumb,
        );
        $model->where($where)->save($data);

        $res = array(
            'id'  => $lastinsid,
            'info' => $info,
            'dir'  => $data,
            'ThumbPath' => $ThumbPath
        );

        // log
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('业务管理/业务编辑/添加新的业务包: '.json_encode($res), $_SESSION['username']);
        }

        $this->ajaxReturn($res);
    }

    /**
     * 编辑业务包
     */
    public function editPackage() {

        $packagetypeid = I('PackageTypeID');

        // 1. 更新数据库基本数据
        $data = array(
            'packagename'        => I('PackageName'),
            'packagedescription' => I('PackageDescription'),
            'price'              => 100 * I('Price'),
            'updatecycletypeid'  => I('UpdateCycleTypeID'),   // 订阅周期
            'packagetypeid'      => $packagetypeid,       // 业务包类型
            'packagetemplateid'  => I('PackageTemplateID'),   // 业务包模板
            'chargetypeid'       => I('ChargeTypeID')         // 计费周期
        );
        $id = I('post.ID');

        // 免费 => 修改chargetype
        if (is_null($data['price']) || $data['price'] == 0) {
            $data['chargetypeid'] = 0;  // chargetypeid 0	免费
        }
        $package = new \Subscribe\Model\PackageModel();

        // D:/software/wamp/www/PushUI/resource/package/
        $confModel = new \Home\Model\ConfigureModel();
        $packagePath = $confModel->getpathbyname("package_path");
        $packagePath = rtrim($packagePath, "\\/") . "/";

        // 旧的图片
        // package86thumb/86_daopisanguan.jpg
        $old = $package->field(array('thumb', 'originaldir', 'qrcode'))->find($id);
        $qrcode = $old['qrcode'];
        if (!file_exists($packagePath.$qrcode)) {
            // 二维码图片
            $data['qrcode'] = $package->saveQrCode($id, $packagetypeid);
            $package->data($data)->where(array('id'=>$id))->save();
        }
        // 没有重新上传图片
        if (0 == $_FILES['pic']['size']) {
            $package->where(array('id' => $id))->save( $data );
            $this->ajaxReturn($data);
        }
        $pic = $_FILES['pic'];
        $filename = $pic['name'];
        // 中文... WTF
        if (!preg_match('/^[A-Za-z0-9\. \ ]+$/', $filename)) {
            $a = explode('/', $pic['type']);
            $ext = array_pop($a);
            $filename = date('YmdHis', time()).str_pad(rand(0,999), 3).'.'.$ext;
        }
        $_FILES['pic']['name'] = $filename;

        $oldThumb = $old['thumb'];
        $parts = explode("/", $oldThumb);
        array_pop($parts);
        $oldThumbAbsPath = $packagePath . $oldThumb;                    // unlink
        unset($parts);
        // D:/software/wamp/www/PushUI/resource/package/package86/86_daopisanguan.jpg
        $oldOriginalPath = $old['originaldir'];
        // 删除旧的图片, 缩略图
        unlink($oldOriginalPath);
        unlink($oldThumbAbsPath);

        // 2. 上传业务包图片
        // 生成上传图片路径函数
        $getSaveName = function($name, $id) {
            // remove ext
            $parts = explode(".", $name);
            array_pop($parts);
            $name = implode(".", $parts);
            return sprintf("%d_%s", $id, $name);
        };
        $getSubName = function($id) {
            return "package" . $id;
        };

        $config = array(
            'maxSize'    => 31457280,
            'rootPath'   => $packagePath,
            'savePath'   => '',
            'saveName'   => array($getSaveName, array('__FILE__', $id)),
            'replace'    => false,
            'exts'       => array('jpg', 'gif', 'png', 'jpeg'),
            // 开启子目录保存 并以packageID（格式为package + [ID], eg: package65）为子目录
            'autoSub'    =>  true,
            'subName'    => array($getSubName, array($id)),
        );
        $upload = new \Think\Upload($config);// 实例化上传类

        $info = $upload->upload();
        if(!$info) {// 上传错误返回错误信息
            $this->ajaxReturn($info);
        }
        // D:\software\wamp\www\PushUI\resource\package\package17\17_xxx.jpg
        $OriginalPath = $packagePath . $info['pic']['savepath'] . $info['pic']['savename'];
        // package26thumb/12yearaslave.jpg
        $ThumbRelDir = "package" . $id . "thumb/";
        // $Thumb 相对于 __ROOT__/resource/package的路径
        $Thumb = $ThumbRelDir. $info['pic']['savename'];

        $ThumbAbsDir = $packagePath . $ThumbRelDir;
        $this->mymkdir($ThumbAbsDir);

        // 业务包缩略图生成
        $image = new \Think\Image();
        $image->open($OriginalPath);

        $width = $confModel->getconfintbyname('thumb_width');
        $height = $confModel->getconfintbyname('thumb_height');

        $ThumbPath = $packagePath . $Thumb;
        // 按照原图的比例生成一个最大为$widthx$height并保存为$ThumbPath
        if ($image->width() != $width || $image->height() != $height) {
            $image->thumb($width, $height, \Think\Image::IMAGE_THUMB_FIXED)->save($ThumbPath);
        } else {
            copy($OriginalPath, $ThumbPath);
        }

        // 3. 存储数据库 业务包图片和缩略图
        $data['originaldir'] = $OriginalPath;
        $data['thumb'] = $Thumb;  // mb_convert_encoding($Thumb, 'UTF-8');
        $package->data( $data )->where(array('id'=>$id))->save();

        $this->ajaxReturn($data);
    }

    /**
     * 删除业务包
     */
    public function delPackage() {
        if (!isset($_GET['id'])) {
            throw_exception("id is not set for delPackage");
        }
        $id = I('get.id');

        $result = array('code'=>0, 'data'=>null);

        $model = new \Subscribe\Model\PackageModel();
        // 查看父级节点有没有其他的孩子节点
        $fields = array('id', 'parentid', 'isnode');
        $res = $model->field($fields)->find($id);
        $parentid = $res['parentid'];
        $child_count = $model->where(array('parentid'=>$parentid))->count();
        if ($child_count <= 1) {
            // 父级业务包 isnode=1
            $model->data(array('isnode'=>1))->where(array('id'=>$parentid))->save();
        }

        $childIdList = array();
        $model->getChildIdListR($childIdList, $id);

        $data = array(
            'id'     => $id,
            'childs' => $childIdList
        );
        $result['data'] = $data;

        if ( count($childIdList) > 0 ) {
            // 有子节点 先删除子节点
            foreach ($childIdList as $item) {
                $code = $model->delOnePackage($item);
                /*
                if ($code < 0) { // error
                    $result['code'] = $code;
                    $this->ajaxReturn($result);
                }
                */
            }
        }
        $result['code'] = $model->delOnePackage($id);

        $this->ajaxReturn($result);
    }

    private function mymkdir($dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        } else if (!is_dir($dir)) {
            unlink($dir);
            mkdir($dir, 0777, true);
        }
    }

    public function getPackageURLPrefix() {
        $model = new \Subscribe\Model\PackageModel();
        $this->ajaxReturn( $model->getPackageURLPrefix() );
    }

    /**
     * 在页面上生成业务包二维码 /program/push?typeid=0
     * url: http://localhost:8003/program/push?typeid=1
     */
    public function packageQRCode() {
        $packageid = I('get.id');
        $id = intval($packageid);
        if ($id == 0) {
            throw_exception('unexpected packageid');
        }
        $uri = '/program/push?typeid='.$id;
        $url = C("QR_URL") . $uri;
        include './ThinkPHP/Extend/Vendor/phpqrcode/phpqrcode.php';
        $errorLevel = 3;  // 容错级别
        $matrixPointSize = 1;       // 生成图片大小
        $margin = 2;
        // 生成二维码图片
        $object = new \QRcode();
        $object->png( $url, false, $errorLevel, $matrixPointSize, $margin );
    }

    /**
     * 取得业务包的深度 根节点为0, 最多5层
     */
    public function getPackageDepth() {
        $id = I('get.id');   // packageid
        $package = new PackageModel();
        $depth = $package->getPackageDepth($id);
        $this->ajaxReturn(array('depth' => $depth));
    }

    public function test() {
        $package = new PackageModel();
        $oid = $package->genPackageOID();
        echo $oid. '<br />';
        echo strlen($oid);
    }

}