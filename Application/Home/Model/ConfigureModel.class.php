<?php
/**
 * Created by PhpStorm.
 * User: mzh
 * Date: 2/12/17
 * Time: 4:33 PM
 */
namespace Home\Model;
use Think\Model;

class ConfigureModel extends Model {
    public function __construct() {
        parent::__construct();
        // 关闭自动检测数据表字段信息
        $this->autoCheckFields = false;
        $this->trueTableName = 'MBIS_Server_Configure';
        C('DB_NAME', './db3/MBIS_Server.db3');
    }

    // @return: array (size=1)
    //          'stringvalue' => string 'xxx'
    public function getconfigbyname($name) {
        $res = $this->field('StringValue')->where(array('Name'=>$name))->select();
        return $res[0];
    }

    public function setconfigstr($name, $value) {
        $data = array(
            'Name'        => $name,
            'StringValue' => $value,
        );
        $res = $this->save($data);
        return $res[0];
    }

    //  return string
    public function getconfstrbyname($name) {
        $res = $this->field('StringValue')->where(array('Name'=>$name))->select();
        return $res[0]['stringvalue'];
    }

    //  return int
    public function getconfintbyname($name) {
        $res = $this->field('IntValue')->where(array('Name'=>$name))->select();
        return $res[0]['intvalue'];
    }

    // @return: string
    public function getpathbyname($name) {
        $resource = __ROOT__ . '/resource';
        if (!file_exists($resource)) {
            mkdir($resource);
        }
        $res = $this->field('StringValue')->where(array('Name'=>$name))->select();
        $dir = $res[0]['stringvalue'];

        // path dos(\) to unix(/)
        $parts = explode('\\', $dir);
        $dir = implode('/', $parts);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    //  path dos2unx @return string
    public function getconfpathbyname($name) {
        $res = $this->field('stringvalue')->where(array('name'=>$name))->select();
        $path = '';
        if (!array_key_exists('stringvalue', $res[0])) {
            return $path;
        }
        $path = preg_replace('/\\\\/', '/', $res[0]['stringvalue']);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }

    public function getcontentpath() {
        $res = $this->field('StringValue')->where(array('Name'=>'content_path'))->select();
        // replace all backslash to slash
        $string = $res[0]['stringvalue'];
        return preg_replace('/\\\\/', '/', $string);
    }

    public function getlogpath() {
        $res = $this->field('StringValue')->where(array('Name'=>'log_path'))->select();
        if (is_null($res[0])) {
            $res[0] = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'resource'
                . DIRECTORY_SEPARATOR . 'media';
        }
        return $res[0];
    }

    public function setlogpath($path) {
        $resp['code'] = 0;
        $path = pathdos2unix($path);

        if (!file_exists($path)) {
            $resp['code'] = -2;
            $resp['msg'] = 'No such file or directory: ' . $path;
            return $resp;
        }
        $data = array('stringvalue'=>$path);

        $res = $this->field('count(id) as n')->where(array('Name'=>'log_path'))->select();
        if (0 < $res[0]['n']) {
//      update
            $this->where(array('Name'=>'log_path'))->save($data);
            $resp['msg'] = 'log path update success';

        } else {
//      insert
            if ($this->create($data)) {
                if ($this->add()) {
                    $resp['code'] = $this->getLastInsID();
                    $resp['msg'] = 'log path insert success';
                } else {
                    $resp['code'] = -3;
                    $resp['msg'] = 'log path insert failed';
                }
            } else {
                $resp['code'] = -1;
                $resp['msg'] = 'create object failed';
            }
        }
        return $resp;
    }

    /**
     * @param $fields array('login','dailyrecord','reviewed')
     * @return array assoc
     */
    public function getIntConfig($fields) {
        $res = array();

        foreach ($fields as $field) {
            $where = array('name'=>$field);
            $t = $this->field('intvalue')->limit(0,1)->where($where)->select();
            $res[$field] = $t[0]['intvalue'];
            unset($t);
        }
        return $res;
    }

    public function getappendixdir() {
        $t = $this->where(array('name'=>'appendix_path'))->field('stringvalue')->select();
        $dir = preg_replace('/\\\\/', '/', $t[0]['stringvalue']);
        if (!is_dir($dir)) {
            @unlink($dir);
            mkdir( $dir, 0777, true );
        }
        $dir = rtrim($dir, "\\/") . "/";
        return $dir;
    }

    /**
     * 缩略图大小
     * @return mixed
     */
    public function getthumbwidth() {
        $t = $this->where(array('name'=>'thumb_width'))->field('intvalue')->select();
        return $t[0]['intvalue'];
    }
    public function getthumbheight() {
        $t = $this->where(array('name'=>'thumb_height'))->field('intvalue')->select();
        return $t[0]['intvalue'];
    }

    /**
     * 背景图大小
     */
    public function getBgWidth() {
        $t = $this->where(array('name'=>'background_width'))->field('intvalue')->select();
        return $t[0]['intvalue'];
    }
    public function getBgHeight() {
        $t = $this->where(array('name'=>'background_height'))->field('intvalue')->select();
        return $t[0]['intvalue'];
    }

    // /PushUI/resource/appendix/
    public function getAppendixURLPrefix($value = 'thumb_path') {
        // $where = array("name" => "appendix_path");
        $where = array("name" => $value);
        $fields = array("stringvalue");
        $rows = $this->where($where)->field($fields)->select();
        // path dos2unix
        $appendix_path = preg_replace('/\\\\/', '/', $rows[0]['stringvalue']);
        $appendix_path = rtrim($appendix_path, "\\/") . "/";
        $root = rtrim($_SERVER['DOCUMENT_ROOT'], "\\/") . "/";
        $len = strlen($root);
        return substr($appendix_path, $len - 1);
    }

    public function getsocketip() {
        $t = $this->where(array('name'=>'local_bind'))->field('stringvalue')->select();
        return $t[0]['stringvalue'];
    }

    public function getsocketport() {
        $t = $this->where(array('name'=>'port'))->field('intvalue')->select();
        return $t[0]['intvalue'];
    }


}
