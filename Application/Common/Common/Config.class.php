<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-16
 * Time: 17:17
 */
namespace Common\Common;

class Config {
    private static $obj = null;

    private $login;
    private $dailyrecord;
    private $reviewed;

    private function __construct() {
        $Configure = new \Home\Model\ConfigureModel();
        $assoc = $Configure->getIntConfig(
            array('login','daily_record','reviewed')
        );
        $this->login = $assoc['login'];
        $this->dailyrecord = $assoc['daily_record'];
        $this->reviewed = $assoc['reviewed'];
        unset($assoc);
    }

    public static function getInstance() {
        if (is_null(self::$obj)) {
            self::$obj = new self();
        }
        return self::$obj;
    }

    public function hasLogin() {
        return $this->login;
    }

    public function hasDailyRecord() {
        return $this->dailyrecord;
    }

    public function hasReviewed() {
        return $this->reviewed;
    }
}