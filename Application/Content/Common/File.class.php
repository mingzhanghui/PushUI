<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-07-20
 * Time: 14:05
 */

namespace Content\Common;

class File {
    public static function pathdos2unix($path) {
        $parts = explode('\\', $path);
        return implode('/', $parts);
    }

    public static function humansize($size) {
        $i = 0;
        $units = array('B', 'K', 'M', 'G', 'T');
        // 最大单位T
        while ($size > 1024 && $i < 5) {
            $size /= 1024;
            $i++;
        }
        return sprintf('%.2f%s', $size, $units[$i]);
    }

    /**
     * scandir except . or ..
     * @param $files: return
     * @param $dir: directory
     */
    public static function listdir(&$files, $dir) {
        if ($handle = opendir($dir)) {
            while (false != ($entry = readdir($handle))) {
                $path = $dir . '/' . $entry;
                if ($entry != '.' && $entry != '..') {
                    $file['name'] = $entry;
                    $file['size'] = self::humansize(filesize($path));
                    $file['time'] = date('Y-m-d', filemtime($path));
                    array_push($files, $file);
                }
            }
        }
        closedir($handle);
    }

    /**
     * recursive version of function listdir
     * @param $files: return
     * @param $dir: directory
     */
    public static function listdir_r(&$files, $dir) {
        if ($handle = opendir($dir)) {
            while (false != ($entry = readdir($handle))) {
                $path = $dir . '/' . $entry;
                if ($entry != '.' && $entry != '..') {
                    if (is_file($path)) {
                        $file['name'] = $entry;
                        $file['size'] = self::humansize(filesize($path));
                        $file['time'] = date('Y-m-d H:i:s', filemtime($path));
                        array_push($files, $file);
                    } else if(is_dir($path)) {
                        self::listdir_r($files, $path);
                    }
                }
            }
        }
        closedir($handle);
    }

    public static function mylistdir($dir) {
        $files = scandir($dir);
        return array_diff($files, array('.', '..'));
    }

    /**
     * 递归列出相对$rootpath下面相对于$rootpath所有文件, 大小+时间+相对路径
     * @param $array
     * @param $rootpath
     */
    public static function recursiveListPath(&$array, $rootpath) {
        $fileinfos = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootpath)
        );
        // $rootpath字符串长度
        $offset = strlen(rtrim($rootpath, '\\/') . '/');

        foreach($fileinfos as $pathname => $fileinfo) {
            if ($fileinfo->isFile()) {

                $size = self::realFileSize($pathname);

                $file['size'] = self::humansize($size);
                $file['time'] = date('Y-m-d', filemtime($pathname));
                // 相对路径
                $name = preg_replace( '/\\\\/', '/', mb_substr($pathname, $offset) );
                $file['name'] = mb_convert_encoding($name, "UTF-8", "GBK");

                array_push($array, $file);
            }
        }
    }

    /**
     * php.ini
     * [COM]
     * com.allow_dcom=true
     * [COM_DOT_NET]
     * extension=php_com_dotnet.dll
     * @param $filePath
     * @return int
     */
    public static function realFileSize($filePath) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $fs = new \COM("Scripting.FileSystemObject");
            return $fs->GetFile($filePath)->Size;
        } else {
            return filesize($filePath);
        }
    }

    /**
     * 删除一个目录下所有的普通文件, 非递归
     */
    public static function emptydir($dir) {
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== '.' && $file !== '..') {
                        unlink($dir.'/'.$file);
                    }
                }
                closedir($dh);
            }
        }
    }

    /**
     * 计算一个目录下文件个数, (非递归)
     * @param $dir
     */
    public static function countDir($dir) {
        $count = 0;
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file !== '.' && $file !== '..') {
                        $count++;
                    }
                }
                closedir($dh);
            }
        }
        return $count;
    }
}