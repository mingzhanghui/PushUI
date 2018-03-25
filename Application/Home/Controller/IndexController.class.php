<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index() {

        // 数据库字段 mapping 模块目录名
        $modules = array(
            'permission' => 'Permission',
            'pushcontrol' => 'Pushctrl',
            'content' => 'Content',
            'subscribe' => 'Subscribe',
            'advertise' => 'Ads',
            'customer' => 'User'
        );

        $urlList = array();
        // $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>','utf-8');
        $config = \Common\Common\Config::getInstance();
        if (! $config->hasLogin()) {
            // 不需要登录, 设置session
            $_SESSION = array(
                'username'    => 'Guest',
            );
            foreach ($modules as $key => $value) {
                $_SESSION[$key] = 1;
                $urlList[$key] = U($value.'/Index/index');
            }
        } else {
            // 需要登录
            foreach ($modules as $key => $value) {
                $urlList[$key] = U($value.'/Login/index');
            }
        }
        unset ( $modules );
        $this->assign( 'list', $urlList );
        $this->display();
    }

    public function test() {

    }
}