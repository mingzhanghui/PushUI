<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-04
 * Time: 18:13
 */

// @param $table string 'Ad_PreRollAdMedia';  => 'AdPrerolladmedia'
function nametabletomodel($table) {

    $parts = explode('_', $table);
    $parts = array_map(function($part) {
        $part = strtolower($part);
        return ucwords($part);
    }, $parts);

    return implode('', $parts);
}