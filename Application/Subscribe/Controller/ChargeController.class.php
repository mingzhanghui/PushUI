<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-02-03
 * Time: 13:02
 */
namespace Subscribe\Controller;
use Content\Model\MediapriceModel;
use Think\Controller;
use Common\Common\Config;

// 业务计费
class ChargeController extends Controller {
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

        $this->model = new \Subscribe\Model\ChargeModel();
    }

    // 电影
    public function index() {
        $this->display();
    }

    // 戏曲
    public function opera() {
        $this->display();
    }

    // 电视剧
    public function tvplay() {
        $this->display();
    }

    // 电视节目
    public function tvprogram() {
        $this->display();
    }

    // 专题节目
    public function special() {
        $this->display();
    }

//   业务 内容计费 左边列表
    public function listMediaCharge() {
        $mediatypeid = I('get.mediatypeid');
        $page = I('get.page');
        $pagesize = I('get.pagesize');

        !isset($pagesize) && $pagesize = 15;

        $page <= 1 && $page = 1;

        $mediatypeid = intval($mediatypeid);

        $res = array();
        $mp  = new MediapriceModel();

        if (in_array($mediatypeid, array(1,3,4,7))) {
            $res = $mp->listMediaCharge( $mediatypeid, $page, $pagesize );
        } else if (in_array($mediatypeid, array(5, 6, 8))) {
            $res = $mp->listSeriesCharge( $mediatypeid, $page, $pagesize );
        } else {
            throw_exception("unexpected mediatypeid: " . $mediatypeid);
        }

        $this->ajaxReturn( $res );
    }

    // 播发任务 一个媒体文件所属 业务包/业务期 列表
    public function listPackageTask() {
        $oid = I('get.oid');
        if ($oid === '') {
            throw_exception(__METHOD__ . ": can't get mediatypeid!");
        }

        $res = $this->model->listPackageTask( $oid );
        $this->ajaxReturn( $res );
    }

    // 播发任务 多个媒体文件所属 业务包/业务期 列表
    public function listMultiPackageTask() {
        if (!isset($_POST['oidstring']) === '' ) {
            throw_exception(__METHOD__ . ": can't post oidstring!");
        }
        $oidstring = I('post.oidstring');
        if (trim($oidstring) === '') {
            $this->ajaxReturn(array());
        }
        $res = $this->model->listMultiPackageTask( $oidstring );
        $this->ajaxReturn( $res );
    }

    /**
     * 费用类型 => 保存  return: array('id'=>xx, 'data'=>xx, 'count'=>xx)
     */
    public function setPriceByOID() {
        $oid = I('get.oid');
        if ('' == $oid) {
            throw_exception(__METHOD__ . ": oid is not set");
        }
        $price = I('get.price');

        $mp = new MediapriceModel();
        $res = $mp->setPriceByOID($oid, $price);

        $this->ajaxReturn( $res );
    }

    /**
     * 取得价格 <= oid
     */
    public function getPriceByOID() {
        $oid = I('get.oid');
        if ('' == $oid) {
            throw_exception(__METHOD__ . ": oid is not set");
        }
        $mp = new MediapriceModel();
        $res = $mp->getPriceByOID($oid);
        $this->ajaxReturn($res);
    }

    // 总集中对应的分集(已提交) 文件名 大小 价格 列表
    // test: S2017031400000000000000000000539, 5
    public function listEpisodes() {
        $oid = I('get.oid');
        $mediatypeid = I('get.mediatypeid');
        if ('' == $oid || '' == $mediatypeid) {
            throw_exception(__METHOD__ . ": oid or mediatypeid is not set");
        }
        $mp = new MediapriceModel();
        $res = $mp->listEpisodes($oid, $mediatypeid);
        // dump($res);
        $this->ajaxReturn( $res );
    }

}