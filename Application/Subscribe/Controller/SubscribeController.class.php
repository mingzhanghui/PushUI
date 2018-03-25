<?php
/**
 * Created by PhpStorm.
 * User: mzh
 * Date: 2017-02-03
 * Time: 11:43
 */
namespace Subscribe\Controller;
use Content\Model\MediaModel;
use Content\Model\PathModel;
use Subscribe\Model\MissionlinkmediaModel;
use Think\Controller;

use Common\Common\Config;
use Common\Common\Logger;
use Pushctrl\Model\PushstatusModel;

class SubscribeController extends Controller {
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
        // 订阅周期
        $cycle = new \Subscribe\Model\SettingsModel();
        $updateCycleTypeList = $cycle->listCycleType();
        $this->assign('updateCycleTypeList', $updateCycleTypeList);

        $package = new \Subscribe\Model\PackageModel();
        // 业务包模板
        $packagetpllist = $package->getpackagetpllist();
        $this->assign('packagetpllist', $packagetpllist);

        // 业务包类型
        $packagetypelist = $package->getpackagetypelist();
        $this->assign('packagetypelist', $packagetypelist);

        // 计费周期
        $chargeTypeList = $package->getChargeTypeList();
        $this->assign('chargeTypeList', $chargeTypeList);

        $this->display();
    }

    /**
     * 所含业务期
     * @param $packageID
     */
    public function listPackageMission() {
        if ( !isset( $_GET['packageid']) ) {
            $this->error(__FUNCTION__ . ": can't get package ID!");
        }
        $packageID = I('get.packageid');
        $model = new \Subscribe\Model\PackageModel();
        $midList = $model->getMissionIdByPackageId($packageID);
        unset($model);

        $missionList = array();
        $model = new \Subscribe\Model\MissionModel();
        foreach ($midList as $id) {
            $mission = $model->getMissionInfoById($id);
            unset($mission['missiondescription']);
            // $mission['status'] = ($mission['synversionid'] > 0) ?  "同步成功" : "未同步";
            $mission['status'] = $mission['synversionid'];
            unset($mission['synversionid']);
            unset($mission['versionid']);
            unset($mission['state']);
            array_push($missionList, $mission);
        }
        $this->ajaxReturn( $missionList );
        // dump($missionList);
    }

    public function getMissionInfo() {
        if ( !isset( $_GET['mid']) ) {
            $this->error(__FUNCTION__ . ": can't get mission ID!");
        }
        $missionID = I('get.mid');
        $model = new \Subscribe\Model\MissionModel();
        $mission = $model->getMissionInfoById($missionID);

        $this->ajaxReturn( $mission );
    }

    public function editMission() {
        if ( !isset( $_POST['id']) ) {
            $this->error(__FUNCTION__ . ": can't get mission ID!");
        }
        $model = new \Subscribe\Model\MissionModel();
        $mission = array(
            "packageid"          => I("post.packageid"),
            "id"                 => I("post.id"),
            "missionname"        => I("post.missionname"),
            "startdate"          => I("post.startdate"),
            "enddate"            => I("post.enddate"),
            "missiondescription" => I("post.missiondescription"),
        );
        $mission = array_filter($mission);

        $res = $model->checkPackageDateRange($mission);
        if ($res['code'] == 0) {    // validate success
            unset($mission['packageid']);
            $res = $model->editMission($mission);
        }
        $this->ajaxReturn( $res );
    }

    // 已经提交并审核
    public function listMissionContent() {
        $mediatypeid = I('get.mediatypeid');
        if (!isset($mediatypeid)) {
            throw_exception(__METHOD__ . ': cannot get mediatypeid');
        }
        $model = new \Subscribe\Model\MissionModel();

        $list = $model->listMediaTypeID();          // 1, 2, 3, 4, 7, 9
        if (!in_array($mediatypeid, $list)) {
            throw_exception(__METHOD__ . ": unexpected mediatypeid " . $mediatypeid);
        }
        $asset_name = trim( I('get.asset_name') );
        $res = $model->listMissionContent($mediatypeid, $asset_name);

        $this->ajaxReturn( $res );
    }

    // 除了总集之外的媒体类型
    public function listMediaType() {
        $where = "id != 0 AND mediatype not like '%总集'";
        $rows = M('mediatype')->where($where)->field(array('id','mediatype'))->select();
        $this->ajaxReturn( $rows );
    }

    public function listNodePackages() {
        $model = new \Subscribe\Model\PackageModel();
        $this->ajaxReturn( $model->listNodePackages() );
    }

    /**
     * 添加新的业务期
     */
    public function addMission() {
        if ( !isset($_POST['PackageID']) ) {
            $this->error(__FUNCTION__ . ": need post PackageID to add mission!");
        }
        $model = new \Subscribe\Model\MissionModel();
        if ( !($model->mycheckdate(I('StartDate')) && $model->mycheckdate(I('EndDate'))) ) {
            $this->error("Unexpected date format");
        }
        $data = array(
            'PackageID' => I("PackageID"),
            'MissionName' => I("MissionName"),
            'StartDate' => I("StartDate"),
            'EndDate' => I("EndDate"),
            'MissionDescription' => I("MissionDescription")
        );
        $data = array_filter($data);
        if ($data['StartDate'] > $data['EndDate']) {
            $this->error("开始日期不能大于结束日期");
        }
        // 没有传入业务期名则根据开始日期和结束日期生成业务期名
        if ( !isset($data['MissionName']) ) {
            // date: 2017-04-01, 2017-04-04 => name: 20170401-20170404
            $data['MissionName'] = preg_replace('/-/', '', $data['StartDate']) .
                '-' . preg_replace('/-/', '', $data['EndDate']);
        }

        $model = new \Subscribe\Model\PackageModel();
        $res = $model->addMission($data);

        // log
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('业务管理/业务管理/添加业务期: '.json_encode($data), $_SESSION['username']);
        }

        $this->ajaxReturn( $res );
    }

    public function delMission() {
        if ( !isset($_GET['mid']) ) {
            throw_exception(__FUNCTION__ . ": GET MissionID is required!");
        }
        $model = new \Subscribe\Model\MissionModel();
        $mid = I("get.mid");
        $res = $model->delMission( $mid );
        unset($model);
        $this->ajaxReturn( $res );
    }

    /**
     * 添加一条内容的到期内容 post
     * @$_POST:
     * array (
     *   'mediaoidlist' => array (
     *     0 => 'CF47F40C5B9676F1CEE45F1021BA9800'
     *     1 => '0B4A4FDA862E6B8DD304E3C880EA6EE5'
     *     2 => 'FC1D25E6626669E91897D2D5083AEFF8'
     *    ),
     *   'date' => '2017-04-18',
     *   'round' => 1
     *);
     */
    public function addContent() {
        $model = new \Subscribe\Model\MissionModel();
        $data = I('post.');

        $res = $model->checkAddContent($data);

        if ($res['code'] < 0) {
            $this->ajaxReturn($res);
        }
        // log
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('业务管理/业务管理/新增内容: '.json_encode($data), $_SESSION['username']);
        }

        $this->ajaxReturn( $model->doAddContent($data) );
    }

    /**
     * 本期内容详情
     */
    public function listMissionLinkMedia() {
        $missionid = I('get.missionid');
        $where = array("missionid" => $missionid);
        $fields = array("id","missionid", "mediaoid", "date", "round");
        $link = new MissionlinkmediaModel();
        $rows = $link->where($where)->field($fields)->select();
        unset($link);

        $Media = new MediaModel();
        $n = count($rows);
        for ($i = 0; $i < $n; $i++) {
            $where = array("oid" => $rows[$i]['mediaoid']);
            $res = $Media->where($where)->field(array("title"))->select();
            $rows[$i]['filename'] = $res[0]['title'];
        }
        $this->ajaxReturn($rows);
    }

    public function getMissionLinkMediaByID() {
        $id = I('get.id');
        $where = array("id" => $id);
        $fields = array("id","missionid", "mediaoid", "date", "round");
        $rows = M("missionlinkmedia")->where($where)->field($fields)->select();
        $this->ajaxReturn( $rows[0] );
    }

    /**
     * 修改本期内容, VersionId+1
     */
    public function editContent() {
        // 接收POST传入参数
        $id = I('post.id');
        $round = I('post.round');
        $date = I('post.date');    // 检查修改日期
        $missionId = I('post.missionid');

        $Mission = new \Subscribe\Model\MissionModel();
        $isValid = $Mission->checkEditContentDate($missionId, $date);
        if (!$isValid) {
            $res = array("code"=>-1, 'msg'=>'修改日期不在业务期范围内!');
            $this->ajaxReturn($res);
        }
        $Mission->versionInc($missionId);
        unset($Mission);

        $Link = new \Subscribe\Model\MissionlinkmediaModel();
        $row = $Link->find($id);

        $flagDate = $flagRound = false;  // 是否修改
        // 修改了日期
        if ($row['date'] !== $date) {
            $oid = $row['mediaoid'];
            $rows = $Link->where(array('date' => $date))->field('mediaoid')->select();
            foreach ($rows as $row) {
                if ($oid === $row['mediaoid']) {
                    $res = array('code' => -2, 'msg' => '该内容已在当天的播发日程中');
                    $this->ajaxReturn($res);
                }
            }
            $flagDate = true;
        }
        // 修改了轮播次数
        if ($row['round'] != $round) {
            $ps = new PushstatusModel();
            $cond = array(
                'missionlinkmediaid' => $id,
                'state'              => 1  // 正在播发
            );
            $t = $ps->where($cond)->field('roundcount')->select();
            if (array_key_exists('roundcount', $t[0])) {
                $roundcount = $t[0]['roundcount'];
            } else {
                $roundcount = 1;  // minimum round count
            }
            if ($round < $roundcount) {
                $res = array('code' => -3, 'msg' => '轮播次数不能小于这个内容的已播发次数('.$roundcount.')');
                $this->ajaxReturn($res);
            }
            $flagRound = true;
        }

        if ($flagDate || $flagRound) {
            $data = array(
                "date"  => $date,
                "round" => $round
            );
            $where = array("id" => $id);
            $res['code'] = $Link->where($where)->data($data)->save();
            if ($res['code'] > 0) {
                $res['msg'] = 'success';
            } else {
                $res['msg'] = '没有变更';
            }
            unset($Link);
        } else {
            $res = array('code' => 0, 'msg' => '没有变更');
        }

        $this->ajaxReturn( $res );
    }

    /**
     * 删除本期内容
     */
    public function delContent() {
        $id = I('get.id');

        $link = new \Subscribe\Model\MissionlinkmediaModel();
        $res = $link->doDelContent($id);

        // log
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('业务管理/业务管理/删除内容(medialinkmedia id): '.$id, $_SESSION['username']);
        }

        $this->ajaxReturn( $res );
    }

    /**
     * 批量删除本期内容
     */
    public function bulkDelcontent() {
        $data = json_decode(stripslashes($_POST['data']));

        $link = new \Subscribe\Model\MissionlinkmediaModel();

        $count = 0;
        foreach ($data as $d) {
            $count += $link->doDelContent($d);
        }
        $this->ajaxReturn($count);
    }

    public function test() {
        $model = new \Subscribe\Model\MissionModel();
        $result = $model->checkEditContentDate(248, "2017-04-19");
        dump( $result);
    }

}