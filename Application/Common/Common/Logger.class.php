<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-17
 * Time: 14:38
 */

namespace Common\Common;

/**
 * ログを取るクラス
 * Usage:
 * // クラス定義読み出し
 * require './Logger.php';
 * // 保存先指定
 * Logger::setpath("./log");
 * // メッセージログ出力
 * Logger::write("保存したいメッセージを書く", 'admin');
 */
class Logger {

    protected static $_on = true; // 出力切替
    protected static $_prefix = '';
    protected static $_ext = '.log';
    protected static $_path = '';
    const MAXSIZE = 1; // rotate size[MB]

    /**
     * ログ出力をオフに
     */
    public static function off() {
        self::$_on = false;
    }
    /**
     * ログ出力をオンに（デフォルト）
     */
    public static function on() {
        self::$_on = true;
    }
    /**
     * メッセージログ出力
     */
    public static function write($s, $user = '') {
        if (self::$_on) {
            $file = self::_getfile($user);
            self::_rotate($user);

            // 日付付加
            $s = rtrim($s);
            if (preg_match('/\n|\r/', $s)) {
                $s = self::_dateline() . PHP_EOL
                    . preg_replace('/(?:\r\n|\r|\n){1,}/', PHP_EOL, $s);
            } else {
                $s = "[" . self::_getdate() . "] " . self::_caller() . "\t" . $s;
            }
            return file_put_contents($file, rtrim($s) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        return '';
    }

    public static function setpath($path) {
        if (!is_dir($path)) {
            mkdir($path, 0777);
        }
        self::$_path = realpath($path);
    }

    public static function setprefix($prefix) {
        self::$_prefix = $prefix;
    }

    protected static function _getdate($short = false) {
        date_default_timezone_set('Asia/Shanghai');
        return date($short ? 'Y-m-d' : 'Y/m/d H:i:s');
    }

    protected static function _dateline() {
        return str_pad(self::_getdate(), 56, "-", STR_PAD_BOTH);
    }

    protected static function _getfile($user, $rotate = false) {
        $file = rtrim(self::$_path, "/\\")
            . DIRECTORY_SEPARATOR
            . self::$_prefix
            . $user
            . ($rotate ? self::_getdate(true) : "")
            . self::$_ext;
        return $file;
    }

    protected static function _rotate($type) {
        $file = self::_getfile($type);
        if (is_writable($file) && filesize($file) > 1024 * 1024 * self::MAXSIZE) {
            rename($file, self::_getfile($type, true));
        }
    }

    protected static function _caller() {
        global $argv;

        if (!empty($argv[0])) {
            $caller = $argv[0];
        } else if (isset($_SERVER['SCRIPT_NAME'])) {
            $caller = basename($_SERVER['SCRIPT_NAME']);
        } else {
            $caller = 'Unknown';
        }

        return $caller;
    }
}