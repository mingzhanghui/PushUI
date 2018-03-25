<?php
/**
 * Created by PhpStorm.
 * User: mzh
 * Date: 2017-02-14
 * Time: 10:36
 */
namespace Ads\Controller;
use Think\Controller;
use Common\Common\Config;

class SyncController extends Controller {
    public static $root;
    private $model;

    public function __construct() {
        parent::__construct();

        if (!isset($_SESSION)) {
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['advertise']) {
                redirect( U('Ads/Login/index').'?refer=' . urlencode(U()) );
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['advertise'] = 1;
        }

        self::$root = $_SERVER['DOCUMENT_ROOT'] . __ROOT__;
        $this->model = new \Ads\Model\SyncModel();
    }

    /**
     * 数据同步 - 广告同步
     */
    public function index() {
        $this->display();
    }

    /**
     * xhr 广告同步 列表
     */
    public function listPush() {
        // 片头广告
        $preroll = $this->model->listPush('preroll');
        // 暂停广告
        $pause = $this->model->listPush('pause');
        $list = array_merge($preroll, $pause);
        $this->ajaxReturn( $list );
    }

    /**
     * 发送广告同步请求
     */
    public function SendSyncSocket() {
        $this->ajaxReturn(
            $this->model->SendSyncSocket()
        );
    }

    /**
     * 查询广告同步状态
     */
    public function SendQuerySocket() {
        $this->ajaxReturn(
          $this->model->SendQuerySocket()
        );
    }

    /**
     * 第2个标签页 查看同步日志
     */
    public function log() {
        $loglist = $this->getLogList();
        $this->assign('loglist', $loglist);
        $this->display();
    }

    public function getLogContent() {
        // $path = self::$root . '/resource/logadv/' . $name;
        $name = I('get.name');
        $path = $this->model->getlogdir() . $name;
        // $content = nl2br(file_get_contents($path));
        $this->ajaxReturn(
            htmlspecialchars( file_get_contents($path) ),
            'EVAL'
        );
    }

    private function getLogList() {
        // 'adlog_path'
        $logdir = $this->model->getlogdir();

        $handle = opendir($logdir);
        if (!$handle) {
            throw_exception("failed to open directory " . $logdir);
        }
        $files = array();
        while (false !== ($entry = readdir($handle))) {
            if ($entry != '.' && $entry != '..') {
                array_push($files, $entry);
            }
        }
        closedir($handle);

        rsort($files, SORT_STRING);

        return $files;
    }

}