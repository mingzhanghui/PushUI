<?php
/**
 * Created by PhpStorm.
 * User: mzh
 * Date: 2/11/17
 * Time: 10:06 PM
 */
function pathdos2unix($path) {
    $parts = explode('\\', $path);
    return implode('/', $parts);
}

function humansize($size) {
    $i = 0;
    $units = array('B', 'K', 'M', 'G');
    // 最大单位G
    while ($size > 1024 && $i < 4) {
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
function listdir(&$files, $dir) {
  if ($handle = opendir($dir)) {
    while (false != ($entry = readdir($handle))) {
      $path = $dir . '/' . $entry;
      if ($entry != '.' && $entry != '..') {
        $file['name'] = $entry;
        $file['size'] = humansize(filesize($path));
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
function listdir_r(&$files, $dir) {
  if ($handle = opendir($dir)) {
    while (false != ($entry = readdir($handle))) {
      $path = $dir . '/' . $entry;
      if ($entry != '.' && $entry != '..') {
        if (is_file($path)) {
          $file['name'] = $entry;
          $file['size'] = humansize(filesize($path));
          $file['time'] = date('Y-m-d H:i:s', filemtime($path));
          array_push($files, $file);
        } else if(is_dir($path)) {
          listdir_r($files, $path);
        }
      }
    }
  }
  closedir($handle);
}

function mylistdir($dir) {
    $files = scandir($dir);
    return array_diff($files, array('.', '..'));
}

/**
 * @param $array
 * @param $pagesize
 * @param $curpage
 */
function arraypage($array, $pagesize = 10, $curpage = 1) {
    $n = count($array);
    // does not need pagination
    if ($n <= $pagesize) {
        return $array;
    }
    $totalpage = $n / $pagesize + 1;
    if ($curpage < 1) {
        $curpage = 1;
    } else if ($curpage > $totalpage) {
        $curpage = $totalpage;
    }
    $start = ($curpage - 1) * $pagesize;
    return array_slice($array, $start, $pagesize);
}

// convert object to array
function object_array($array) {
    if (is_object($array)) {
        $array = (array)$array;
    }
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = object_array($value);
        }
    }
    return $array;
}

function upload_image($file, $path = null, $type = array('image/png', 'image/jpeg', 'image/gif')) {
  if (is_uploaded_file($file['tmp_name'])) {
    // 限制文件类型为图片
    if (!in_array($file['type'], $type)) {
      return false;
    }
    /* 限制文件大小 < 10M */
    if ($file['size'] > 10485760) {
      return false;
    }
    // 生成上传之后文件名
    $filename = $file['name'];
    // 生成上传之后文件路径
    if (is_null($path)) {
      $path = __ROOT__ . '/resource/appendix';
      if (!is_dir($path)) {
        mkdir($path);
      }
      $returnFile = $filename;
    } else {
      $path = rtrim($path, '/') . '/';
      $returnFile = $path . $filename;
    }
    $filePath = $path . $filename;
    // 确保文件是有效的上传文件
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
      return $returnFile;
    } else {
      return false;
    }
  } else {
    return false;
  }
}

