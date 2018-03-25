<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-03
 * Time: 13:21
 */
namespace Subscribe\Controller;
use Think\Controller;
use Common\Common\Config;

class SettingsController extends Controller {
    private $model = null;

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
        $this->model = new \Subscribe\Model\SettingsModel();
    }

    public function index() {
        $this->display();
    }

    public function listCycleType() {
        $cycletypelist = $this->model->listCycleType();
        $this->ajaxReturn( $cycletypelist );
    }

    public function editCycleType() {

        if (!isset($_POST['id']) || !isset($_POST['updatecycletype'])) {
            throw_exception(__METHOD__ . ' id, update... is not set!');
        }
        $id = I('post.id');
        $data = array(
            'updatecycletype'        => I('post.updatecycletype'),
            'updatecycledescription' => I('post.updatecycledescription')
        );
        $this->model->editperiodtype($id, $data);
        echo "<script>history.go(-1)</script>";
    }


    public function addCyleType() {
        if (IS_POST) {
            $data = array(
                'updatecycletype'        => I('post.updatecycletype'),
                'updatecycledescription' => I('post.updatecycledescription')
            );
            $id = $this->model->addCycleType($data);
            $msg = 'add cycle type success';
        } else {
            $id = -1;
            $msg = '$_POST is not set!';
        }
        $this->ajaxReturn(array('id'=>$id, 'msg'=>$msg));
    }

    public function delCycleType() {
        $this->model->delperiodtype(I('get.id'));
        echo "<script>history.go(-1)</script>";
    }

    public function setlogpath() {
        $where = array('name'=>'log_path');

        if (!isset($_POST['stringvalue'])) {
            throw_exception(__METHOD__ . ': cannot post stringvalue');
        }
        $data = I('post.');

        $msg = '';
        if (!is_dir($data['stringvalue'])) {
            $code = 2;
            $msg = '目录不存在';
        } else {
            $code = M('configure')->where($where)->data($data)->save();
            $code == 1 && $msg = '路径修改成功';
        }
        $this->ajaxReturn(array('code' => $code, 'msg'=>$msg));
    }

    public function getlogpath() {
        $model = new \Subscribe\Model\SyncModel();
        $dir = $model->getLogDir();
        $this->ajaxReturn(array('dir' => $dir));
    }

}