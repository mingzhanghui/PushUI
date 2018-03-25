<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-20
 * Time: 14:04
 */
namespace Content\Model;
use Content\Common\File;
use Home\Model\ConfigureModel;
use Think\Model;

class AppendixModel extends Model {
    protected $tableName = 'appendix';
    protected $fields = array('id', 'attachoid', 'appendixtypeid', 'url', 'filename', 'size');
    protected $pk = 'id';

    /**
     * 通过附件所属媒体文件OID 取得附件url等内容列表
     * @param $attachoid
     * @return array
     * [0] => array("appendixtypeid" => 2(or: 1), "url"=>[oid]/filename...),
     * [1] => array("appendixtypeid" => 1(or: 2), "url"=>[oid]/filename...)
     */
    public function listAppendixByOID($attachoid) {
        $condition = array('attachoid' => $attachoid);
        $fileds = array(
            'ID',
            'AttachOID',
            'AppendixTypeID',
            'URL',
            'Filename',
            'Size',
        );
        $result = $this->field($fileds)->where($condition)->select();

        $types = M('appendixtype')->select();
        $map = array();
//  @$types:
        //  array(
        //    0 => array(
        //      'id'           => '1',
        //      'appendixtype' => '缩略图'
        //    ),
        //		1 => array(
        //      'id'           => '2',
        //      'appendixtype' => '海报'
        //    )
        //	);
        // @$map: Array ([1] => 缩略图, [2] => 海报)
        foreach ($types as $type) {
            $map[$type['id']] = $type['appendixtype'];
        }

        $C = new \Home\Model\ConfigureModel();

        foreach ($result as $i => $row) {
            $typeid = $row['appendixtypeid'];

            $result[$i]['type'] = $map[$typeid];
            $result[$i]['size'] = File::humansize($row['size']);

            // /resource/appendix/
            // $prefix = substr($appendixDir, strlen($_SERVER['DOCUMENT_ROOT'] . __ROOT__));
            if ($typeid == 1) {
                $dir = $C->getconfpathbyname('thumb_path');
            } else if ($typeid == 2) {
                $dir = $C->getconfpathbyname('background_path');
            } else {
                $dir = $C->getappendixdir();
            }
            $dir = rtrim($dir, '\\/').'/';
            $prefix = substr($dir, strlen($_SERVER['DOCUMENT_ROOT']));
            $prefix = '/' . ltrim($prefix, '\\/');

            $result[$i]['url'] = $prefix . $result[$i]['url'];
        }

        return $result;
    }

    /**
     * @param $attach  array("attachoid"=>"xxx", "appendixtypeid"=>"xxx")
     * @return int
     *  yes:   [id]
     *  no:     0
     */
    public function hasAppendix($attach) {
        $rows = $this->field(array("id"))->where($attach)->select();
        if (count($rows) > 0) {
            return $rows[0]['id'];
        }
        return 0;
    }

    public function getThumbUrl($oid) {
        $model = new \Home\Model\ConfigureModel();
        $prefix = $model->getAppendixURLPrefix(); // xxx/

        $where = array(
            "attachoid" => $oid,
            "appendixtypeid" => 1,
        );
        $rows = $this->where($where)->field(array("url"))->select();
        $url = rtrim($prefix, "\\/") . '/' . $rows[0]['url'];
        return $url;
    }

    /**
     * 上传附件
     * @param $file array
     * array (size=5)
     *  'name' => string '255293.jpg' (length=10)
     *  'type' => string 'image/jpeg' (length=10)
     *  'tmp_name' => string 'D:\software\wamp\tmp\php915E.tmp' (length=32)
     *  'error' => int 0
     *  'size' => int 109755
     *
     * @param $attach array('attachoid'=>xxx, 'appendixtypeid'=>1/2)
     *
     * @return int table "appendix" last insert id
     */
    public function upload($file, $attach) {
        $C = new \Home\Model\ConfigureModel();
        $appendixDir = $C->getappendixdir();
        // D:\software\wamp\www\mibs\UI\test_pushed\resource\appendix\799379f2e27390788da1a47b5f7f4a4a
        $oid = $attach['attachoid'];
        $appendixDir = rtrim($appendixDir, "\\/") . "/" . $oid;
        if (!file_exists($appendixDir)) {
            mkdir($appendixDir, 0777, true);
        }
        // destination file path
        if (mb_detect_encoding($file['name']) === 'ASCII') {
            $filename = $file['name'];
        } else {
            // 非ascii字符图片名称 需要重命名 否则页面不能展示
            $parts = explode(".", $file['name']);
            $suffix = array_pop($parts);

            // 后缀名不像图片的情况
            if (!in_array($suffix, array('bmp', 'jpg', 'jpeg', 'gif', 'png'))) {
                $suffix = 'jpg';
            }
            $rand = rand(0, 1000);
            $date = date("YmdHis", time()) . str_pad($rand, 3, "0", STR_PAD_LEFT);
            $filename = $date . '.' . $suffix;
        }
        $filepath = $appendixDir . '/' . $filename;

        // upload
        if (!is_uploaded_file($file['tmp_name'])) {
            throw_exception('Unexpected file which is not an uploaded file');
        }
        move_uploaded_file($file['tmp_name'], $filepath);

        // make thumb
        $image = new \Think\Image();

        $image->open($filepath);

        //  按照原图的比例生成一个最大为 132*96 的缩略图(132*96, 178*250)
        if (1 == $attach['appendixtypeid']) {
            // thumb
            $x = $C->getthumbwidth();
            $y = $C->getthumbheight();
            $dstPath = implode('/', array(
                $C->getconfpathbyname('thumb_path'),
                $oid
            ));
        } else {
            // background
            $x = $C->getBgWidth();
            $y = $C->getBgHeight();
            $dstPath = implode('/', array(
                $C->getconfpathbyname('background_path'),
                $oid
            ));
        }
        if (!is_dir($dstPath)) {
            mkdir($dstPath, 0777, true);
        }
        $dstPath .= '/'.$filename;

        $width = $image->width();
        $height = $image->height();
        if ($width == $x && $height == $y) {
            // 直接copy
            copy($filepath, $dstPath);
        } else {
            // 缩放后生成一个固定大小的图片
            $image->thumb($x, $y,\Think\Image::IMAGE_THUMB_FIXED)->save($dstPath);
        }
        // 缩略图大小
        $size = filesize($dstPath);
        unset($C);

        $attach['url'] = $attach['attachoid'] . '/' . $filename;
        $attach['filename'] = $filename;
        $attach['size'] = $size;

        // try write to db
        $this->add($attach);

        // write to db failed, unlink file
        $id = $this->getLastInsID();
        if (!(intval($id) > 1)) {
            unlink($filepath);
        }
        return $id;
    }

    /**
     * 删除附件
     * @param $id string @table: appendix pk
     */
    public function removeAppendix($id) {
        $where = array('id' => $id);

        $C = new ConfigureModel();
        $appendixDir = $C->getappendixdir();

        $fields = array("attachoid", "url");
        $rows = $this->where($where)->field($fields)->select();
        $url = $rows[0]['url'];
        unset($rows);

        $result = array('db' => 0, 'file' => 0);
        $count = $this->where($where)->count();
        $t = $this->where($where)->delete();
        if ($count > 0) {
            $result['db'] = $t;
        } else {
            // 数据库中不存在这个附件
            $result['db'] = 1;
        }
        M()->execute('vacuum');

        if (is_null($url)) {
            $result['file'] = 1; // appendix url in db is not found
        } else {
            $filepath = $appendixDir . '/' . $url;
            if (file_exists($filepath)) {
                $result['file'] = unlink($filepath);
                $dir = dirname($filepath);
                // empty dir . and ..
                $files = scandir($dir);
                // If all appendix has been removed and the dir is empty, rmdir . or ..
                if (2 === count($files)) {
                    rmdir($dir);
                }
            } else {
                $result['file'] = 1; // unnecessary to delete
            }
        }

        return $result;
    }

    // 通过media oid删除 缩略图和海报  __ROOT__/resource/appendix/[0-9A-Z]{32}/*.[jpg|jpeg|png|gif]
    public function delAllAppendix($oid) {
        $model = new \Home\Model\ConfigureModel();
        // appendix directory
        $dir = $model->getappendixdir();
        $dir = rtrim($dir, "\\/") . "/";
        if (!is_dir($dir)) {
            $dir = $_SERVER['DOCUMENT_ROOT'] . __ROOT__ . '/resource/appendix/';
        }
        $dir .= strtoupper($oid);
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    $path = rtrim($dir, "\\/") . "/" . $file;
                    unlink($path);
                }
                closedir($dh);
            }
        }
        rmdir($dir);
    }



}