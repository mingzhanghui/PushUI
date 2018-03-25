<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-13
 * Time: 13:26
 */
namespace Pushctrl\Controller;
use Think\Controller;
use Common\Common\Config;

class QueryController extends Controller {
    private $model = null;

    public function __construct() {
        parent::__construct();
        if (!isset($_SESSION)) {
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['pushcontrol']) {
                redirect( U('Pushctrl/Login/index').'?refer=' . urlencode(U()) );
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['pushcontrol'] = 1;
        }
        $this->model = new \Pushctrl\Model\QueryModel();
    }

    public function index() {
        $date = I('get.date');
        if (!isset($date) || $date == '') {
            $date = date('Y-m-d', time());
        }
        $this->assign('date', $date);

        $model = new \Subscribe\Model\BroadcastModel();
        $ul = $moveLeft = $moveRight = '';
        $model->dateList($ul, $moveLeft, $moveRight, $date);

        $this->assign('ul', $ul);
        $this->assign('moveLeft', $moveLeft);
        $this->assign('moveRight', $moveRight);

        $tasklist = $this->model->listTask($date);
        $this->assign('tasklist', $tasklist);

        $empty = '<tr><td colspan="8" class="text-center">查询任务结果为空</td></tr>';
        $this->assign('empty', $empty);

        $this->display();
    }

}