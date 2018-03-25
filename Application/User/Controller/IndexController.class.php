<?php
namespace User\Controller;
use Think\Controller;
use User\Model\CustomerModel;
use Subscribe\Common\Date;

use Common\Common\Config;
use Common\Common\Logger;

class IndexController extends Controller {
    public function __construct() {
        parent::__construct();

        if (!isset($_SESSION)) {
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['customer']) {
                redirect( U('User/Login/index').'?refer=' . urlencode(U()) );
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['customer'] = 1;
        }
    }

    /**
     * 新用户开户
     */
    public function index() {
        $this->display();
    }

    /**
     * 用户信息查询
     */
    public function query() {
        $this->display();
    }

    /**
     * 用户量统计
     */
    public function stat() {
        $Customer = new \User\Model\CustomerModel();
        $count = $Customer->count();
        $this->assign('count', $count);
        $this->display();
    }

    /**
     * 批量导入用户机顶盒信息, iframe upload
     */
    public function bulkImport() {

        $file = $_FILES['excel'];
        $types = array(
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        );
        if (!in_array($file['type'], $types)) {
            $this->iframeReturn('不是Excel文件类型: ' . $file['type']);
        }
        $path = $file['tmp_name'];
        if (!is_uploaded_file($path)) {
            $this->iframeReturn('不是合法的上传文件');
        }

        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.IOFactory");
        $objPHPExcel = \PHPExcel_IOFactory::load($path);
        $worksheet = $objPHPExcel->getActiveSheet();

        $cnt = $worksheet->getHighestRow();

        // 需要导入的列
        $fields = array('stbid', 'starttime', 'endtime');

        $msg = '';
        $today = date('Y-m-d', time());
        $Customer = new CustomerModel();
        // $enddate = date('Y-m-d', time()+86400*365);
        $ra = $re = 0;

        for ($i = 2; $i <= $cnt; $i++) {
            // 导入一行记录
            $row = array();
            foreach ($fields as $ii => $field) {
                // 'A', 'B', 'C'
                $col = chr(ord('A') + $ii);
                $v = $worksheet->getCell($col . $i)->getValue();
                $row[$field] = trim($v, " '");
            }
            // 判断STBID
            if (!CustomerModel::STBIDCheck($row['stbid'])) {
                $msg .= "STBID: " . $row['stbid'] . "不是16位的数字<br />";
                continue;
            }
            // 转化数字为日期42736 43101
            $row['starttime'] = CustomerModel::excelTime($row['starttime']);
            $row['endtime'] = CustomerModel::excelTime($row['endtime']);

            // 判断起始时间StartTime
            if ($row['starttime'] == '') {
                $row['starttime'] = $today;
            }
            $StartTimeResult = CustomerModel::TimeCheck($row['starttime']);
            if ($StartTimeResult == false) {
                $msg .= sprintf("开始时间%s不合法", $row['starttime']);
                continue;
            }
            // 判断结束时间
            ($row['endtime'] == '') && $row['endtime'] = '2099-12-30';

            $EndTimeResult = CustomerModel::TimeCheck($row['endtime']);
            if ($EndTimeResult == false) {
                $msg .= sprintf("结束时间%s不合法<br />", $row['endtime']);
                continue;
            }
            $data = array(
                'starttime' => $row['starttime'],
                'endtime' => $row['endtime'],
                'customerdateadded' => $today,
                'customerstate' => 1,
            );
            $where = array('stbid' => $row['stbid']);
            $t = $Customer->field('stbid')->where( $where )->limit(0,1)->select();

            if (is_null($t[0]['stbid']) || $t[0]['stbid'] === '') {
                // insert
                $data['stbid'] = $row['stbid'];
                $id = $Customer->data($data)->add();
                if ($id > 0) {
                    $ra++;
                }
            } else {
                // update
                if (isset($data['stbid'])) {
                    unset($data['stbid']);
                }

                $re += $Customer->where($where)->data($data)->save();
            }
        }

        set_time_limit(0); // no timeout
        $msg = sprintf('导入%d, 更新%d条用户数据', $ra, $re);

        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('用户管理/新用户开户/批量导入: '.$msg, $_SESSION['username']);
        }

        $this->iframeReturn($msg);
    }

    /**
     * 在iframe页里面向外面的页面发送数据 http://qiita.com/yasumodev/items/d339a875b4b9bf65d156
     * @param $msg
     */
    private function iframeReturn($msg) {
        echo "<script type='text/javascript'>window.addEventListener('message',function(event) {event.source.postMessage('"
            . $msg . "', event.origin);}, false);</script>";
        die;
    }

    /**
     * 导入单个用户信息
     * @param $msg
     */
    public function import() {
        $result = array('code' => 0, 'msg' => '');

        $stbid = I('get.stbid');
        $starttime = I('get.starttime');
        $endtime = I('get.endtime');

        if ($stbid === '') {
            $result['code'] = 1;
            $result['msg'] = '机顶盒ID不能为空';
            $this->ajaxReturn($result);
        } else {
            $stbid = trim($stbid, "' \t");

            preg_match('/^[A-Za-z0-9]+$/', $stbid, $matches);
            if (count($matches) === 0) {
                $result['code'] = 1;
                $result['msg'] = '机顶盒ID只能是字母和数字组合';
                $this->ajaxReturn($result);
            }
            $stbid = CustomerModel::formatStbid($stbid);
        }

        if ($starttime === '') {
            $result['code'] = 2;
            $result['msg'] = '机顶盒有效期开始时期不能为空';
            $this->ajaxReturn($result);
        } else if (! Date::mycheckdate($starttime) ) {
            $result['code'] = 2;
            $result['msg'] = '机顶盒有效期开始时期不是yyyy-mm-dd格式';
            $this->ajaxReturn($result);
        } else {
            // 开始日期和结束日期需要格式化 2017-1-1  => 2017-01-01
            $starttime = CustomerModel::formatDate($starttime);
        }

        if ($endtime === '') {
            $result['code'] = 3;
            $result['msg'] = '机顶盒有效期结束时期不能为空';
            $this->ajaxReturn($result);
        } else if (!Date::mycheckdate($endtime)) {
            $result['code'] = 2;
            $result['msg'] = '机顶盒有效期开始时期不是yyyy-mm-dd格式';
            $this->ajaxReturn($result);
        } else {
            $endtime = CustomerModel::formatDate($endtime);
        }

        if ($starttime > $endtime) {
            $result['code'] = 3;
            $result['msg'] = '机顶盒有效期结束日期不能小于开始日期';
            $this->ajaxReturn($result);
        }

        $data = array(
            'stbid' => $stbid,
            'starttime' => $starttime,
            'endtime' => $endtime,
            'customerdateadded' => date('Y-m-d', time()),
            'customerstate' => 1,
        );
        $where = array('stbid' => $stbid);
        $Customer = new \User\Model\CustomerModel();
        $t = $Customer->where($where)->field('stbid')->select();
        if (is_null($t[0]['stbid'])) {
            // insert
            $res = $Customer->data($data)->add();
            if ($res > 0) {
                $result['code'] = 0;
                $result['msg'] = '添加用户机顶盒记录成功: ' . $res;
            }
        } else {
            unset($data['stbid']);
            $res = $Customer->data($data)->where($where)->save();
            if ($res > 0) {
                $result['code'] = 0;
                $result['msg'] = '更新' . $res . '条用户机顶盒记录';
            }
        }

        $this->ajaxReturn($result);
    }

    /**
     * 查询用户 xhr
     */
    public function xhrsearch() {
        $get = array_filter(I('get.'));
        $names = array('stbid', 'customerdateadded', 'starttime', 'endtime');
        $where = array();
        foreach ($get as $name => $value) {
            if ($name === 'stbid') {
                $where[$name] = array('like', '%' . $value . '%');
            } else {
                if (in_array($name, $names)) {
                    $where[$name] = array('eq', $value);
                }
            }
        }
        $pagesize = 10;
        $p = $get['p']; // 当前页数
        if (!isset($p) || $p === '') {
            $p = 1;
        }
        $Customer = new \User\Model\CustomerModel();
        $total = $Customer->where($where)->count();
        $totalpages = ceil($total / $pagesize);
        if ($p > $totalpages) {
            $p = $totalpages;
        }
        $offset = ($p - 1) * $pagesize;
        $fields = array('customerid, stbid, customerdateadded, customerstate', 'starttime', 'endtime');
        $rows = $Customer->where($where)->field($fields)->limit($offset, $pagesize)->order('customerid desc')->select();
        $result = array(
            'get' => $get,
            'pagesize' => $pagesize,
            'p' => $p,
            'totalpages' => $totalpages,
            'total' => $total,
            'offset' => $offset,
            'rows' => $rows,
        );
        $this->ajaxReturn($result);
    }

    /**
     * 用户信息详细信息 取得
     */
    public function getCustomerInfo($customerid) {
        $Customer = new \User\Model\CustomerModel();
        $obj = $Customer->find( $customerid );
        $this->ajaxReturn($obj);
    }

    public function setCustomerInfo() {
        $data = I('get.');
        $customerid = $data['customerid'];
        if ($customerid === '') {
            $this->ajaxReturn(array('code' => -1, 'msg' => '你还没有选择要修改的用户!'));
            // E(__METHOD__ . ': customerid is not set');
        }
        unset($data['customerid']);
        $where = array('customerid' => $customerid);

        $stbid = $data['stbid'];
        if ($stbid === '') {
            $result['code'] = 1;
            $result['msg'] = '机顶盒ID不能为空';
            $this->ajaxReturn($result);
        } else {
            $stbid = trim($stbid, "' \t");

            preg_match('/^[A-Za-z0-9]{16}$/', $stbid, $matches);
            if (count($matches) === 0) {
                $result['code'] = 1;
                $result['msg'] = '机顶盒号码只能是16位字母和数字组合';
                $this->ajaxReturn($result);
            }
            // $data['stbid'] = $this->model->formatStbid($stbid);
        }

        $Customer = new \User\Model\CustomerModel();
        $t = $Customer->where($where)->data($data)->save();
        if ($t > 0) {
            $code = 0;
            $msg = '修改成功';
        } else {
            $code = -2;
            $msg = '写入数据库失败';
        }
        $this->ajaxReturn(array('code' => $code, 'msg' => $msg));
    }

    /**
     * 删除机顶盒记录
     */
    public function deleteCustomer($customerid) {
        $Customer = new \User\Model\CustomerModel();
        // @pk: customerid
        $obj = $Customer->field('stbid')->find($customerid);
        $stbid = $obj['stbid'];
        unset($obj);
        $t = $Customer->delete($customerid);
        if ($t > 0) {
            M()->execute('vacuum');
        }
        $this->ajaxReturn(array('n' => $t, 'stbid' => $stbid)); // rows count
    }

    /**
     * 批量删除 机顶盒记录
     */
    public function bulkDeleteCustomer() {
        $string = I('get.string');
        $listId = explode(',', $string);

        $Customer = new \User\Model\CustomerModel();
        $where['customerid'] = array('in', $listId);

        $t = $Customer->where($where)->delete();
        if ($t > 0) {
            M()->execute('vacuum');
        }
        $this->ajaxReturn(array('n' => $t)); // rows count
    }

    /**
     * 取得导出数据条目数
     */
    public function getExportCount() {
        $start = I('get.start');
        $end = I('get.end');
        $map['customerdateadded'] = array('BETWEEN', $start . ',' . $end);
        $Customer = new \User\Model\CustomerModel();
        $cnt = $Customer->where($map)->count();
        $this->ajaxReturn(array('cnt' => $cnt));
    }

    public function excelExport() {
        $start = I('get.start');
        $end = I('get.end');
        $map['customerdateadded'] = array('BETWEEN', $start . ',' . $end);
        $Customer = new \User\Model\CustomerModel();

        $fields = array('stbid', 'starttime', 'endtime');
        $data = $Customer->where($map)->field($fields)->select();
        foreach ($data as $i =>$row) {
            $data[$i]['stbid'] = "'". strval($data[$i]['stbid']);
        }
        $this->_doExcelExport($data, $fields, 'customer');
    }

    /**
     * 导出数据为excel表格
     * @param $data    array 一个二维数组,结构如同从数据库查出来的数组
     * @param $title   string excel的第一行标题,一个数组,如果为空则没有标题
     * @param $filename  string 下载的文件名
     * @example
    $stu = M ('User');
    $arr = $stu -> select();
    exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
     */
    private function _doExcelExport(&$data=array(),$title=array(),$filename='report') {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=".$filename.".xlsx");
        header("Pragma: no-cache");
        header("Expires: 0");

        // 1. 引入phpExcel类
        import("Org.Util.PHPExcel");
        // 2. 实例化一个phpexcel
        $objPHPExcel = new \PHPExcel();

        //  Limits the maximum execution time unlimit
        set_time_limit(0);

        // 设置 在第一个标签中写入数据 设置活动的sheet是第一个 从0开始
        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        // 导出xls 开始
        $letter = array();
        // 根据$title个数生成excel表的列A, B, C...
        if (!empty($title)) {
            foreach ($title as $i => $field) {
                array_push($letter, chr(ord('A') + $i));
                $sheet->setCellValue($letter[$i]."1", $field);
            }
        } else {
            foreach ($data[0] as $i => $field) {
                array_push($letter, chr(ord('A') + $i));
            }
        }
        if (!empty($data)) {
            foreach ($data as $i => $row) {
                $ii = 0;   // from 'A',...
                foreach ($data[$i] as $key => $value) {
                    // 从第2行开始
                    $sheet->setCellValue($letter[$ii].strval($i+2), "'".strval($value));
                    $ii++;
                }
            }
        }

        // 保存
        import("Org.Util.PHPExcel.IOFactory");
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        ob_clean();    // 关键
        flush();       // 关键
        $objWriter->save('php://output');
        exit;
    }

}