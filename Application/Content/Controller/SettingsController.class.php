<?php
/**
 * Created by PhpStorm.
 * User: mzh
 * Date: 2017-01-23
 * Time: 16:23
 */
namespace Content\Controller;
use Think\Controller;

use Common\Common\Config;
use Common\Common\Logger;

class SettingsController extends Controller {
    private $model;

    public function __construct() {
        parent::__construct();
        // ?login
        if(!isset($_SESSION)){
            session_start();
        }
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['content']) {
                redirect(U('Content/Login/index').'?refer='.U());
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['content'] = 1;
        }

        $this->model = new \Content\Model\SettingsModel();
    }

    public function __destruct() {unset($this->model);$this->model = null;}

    public function index() {
        $mediatypes = $this->model->listMediaTypes();
        $appendixtypes = $this->model->listAppendixTypes();
        $genretypes = $this->model->listGenreTypes();
        $countrytypes = $this->model->listCountry();
        $yeartypes = $this->model->listYear();

        $this->assign("mediatypes", $mediatypes);
        $this->assign("appendixtypes", $appendixtypes);
        $this->assign("genretypes", $genretypes);
        $this->assign("countrytypes", $countrytypes);
        $this->assign("yeartypes", $yeartypes);

        $this->display();
    }

    // 剧情类型
    public function listGenre() {
        $genretypes = $this->model->listGenreTypes();
        $this->ajaxReturn($genretypes);
    }

    public function addMediaType() {
        $mediatype = I("get.mediatype");
        $mediatype = trim($mediatype);

        $res = array('code'=>-1, 'msg'=>'add media type failed!');
        if (isset($mediatype) && $mediatype != '') {
            $data = array("mediatype" => $mediatype);
            $model = M("mediatype");
            $model->data($data)->add();
            $res['code'] = $model->getLastInsID();
            $res['msg'] = $mediatype;
        }
        $this->ajaxReturn($res);
    }

    // e.g. @field: mediatype, @table: mediatype
    public function listTypes() {
        $field = I('get.field');
        $table = I('get.table');
        if (!isset($field)) {
            throw_exception(__METHOD__ . " field is not set!");
        }
        $rows = M($table)->field(array("id", $field))->where("id!=0")->order('id desc')->select();
        // [{'id':xx, 'name':xx},{...}...]
        array_walk($rows, function(&$row, $i, $type) {
            $row['name'] = $row[$type];
            unset($row[$type]);
        }, $field);

        $this->ajaxReturn($rows);
    }

    // id, table, name
    public function editType() {
        $id = I('get.id');
        $table = I('get.table');
        $name = I('get.name');

        $data = array($table => $name);
        $where = array('id' => $id);
        $res['code'] = M($table)->data($data)->where($where)->save();
        $this->ajaxReturn($res);
    }
    // table, name
    public function addType() {
        $table = I('get.table');
        $name = I('get.name');

        $data = array($table => $name);
        $res['code'] = M($table)->data($data)->add();
        $this->ajaxReturn($res);
    }
    // id, table
    public function delType() {
        $table = I('get.table');
        if (!in_array($table, array("genre","country","year"))) {
            throw_exception(__METHOD__ . "unexpected table");
        }
        $id = I('get.id');
        $count = $this->model->countMediaByTypeID($table, $id);
        $res = array();
        if ($count > 0) {
            $res['code'] = -1;
            $res['msg'] = '该分类下有' .$count. '个媒体文件, 不能删除!';
            $this->ajaxReturn($res);
        }
        $code = $this->model->delType($table, $id);
        $res['code'] = $code;
        if ($code > 0) {
            $res['msg'] = '删除分类成功!';
        }
        $this->ajaxReturn($res);
    }

    // key => value
    public function listConfig() {
        // dump( $this->model->listConfig() );
        $this->ajaxReturn( $this->model->listConfig() );
    }

    public function putConfig() {
        $data = I('post.');
        $Config = M("configure");

        $count = 0;
        foreach ($data as $key => $value) {
            $where = array("name" => $key);
            preg_match("/^[0-9]+$/", $value, $matches);

            if (count($matches) > 0) {   // integers
                $a = array("intvalue"=>$value);
            } else {
                $a = array("stringvalue"=>$value);
            }
            $n = $Config->where($where)->data($a)->save();
            $count += $n;

            // if (array_key_exists('stringvalue', $a)) {@mkdir($value, 0777, true);}
        }
        $config = Config::getInstance();
        if ($config->hasDailyRecord()) {
            Logger::setpath(C('DAILY_RECORD_DIR'));
            Logger::write('内容编辑/管理设置: '.json_encode($_POST), $_SESSION['username']);
        }
        $this->ajaxReturn(array("count"=>$count));
    }

    public function test() {
        $s = '123.5';
        preg_match("/^[0-9]+$/", $s, $matches);
        dump($matches);
        $s = '/test/t3st1ng/xxx1';
        preg_match("/^[0-9]+$/", $s, $matches);
        dump($matches);
    }

}