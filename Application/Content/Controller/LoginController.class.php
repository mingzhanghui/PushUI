<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-16
 * Time: 11:19
 */
namespace Content\Controller;
use Think\Controller;

class LoginController extends Controller {
    public function index($refer='') {
        if(!isset($_SESSION)) {
            session_start();
        }

        if (isset($_SESSION['username']) && $_SESSION['content']) {
            $refer = I('get.refer');
            $refer = ($refer === '') ? U('Content/Index/index') : urldecode($refer);
            redirect($refer);
        }

        if(IS_POST) { //登录验证
            $username = I('post.username');
            $password = I('post.password');
            $User = new \Permission\Model\UserModel();
            $uid = $User->login($username, $password); //获取数据库中保存的用户ID

            if(0 < $uid) { // 登录成功
                // 判断进入权限管理系统的权限
                if ( !$User->permission($uid, 'content') ) {
                    $this->error($username.'没有权限访问内容管理');
                }

                // 将用户名存入session
                $_SESSION['username'] = $username;
                $_SESSION['content'] = 1;

                // 跳转到登录前页面
                $refer = I('get.refer');
                $refer = ($refer === '') ? U('Content/Index/index') : urldecode($refer);
                $this->success('登录成功！', $refer);

            } else { //登录失败
                switch($uid) {
                    case -1: $error = '用户不存在!'; break; //系统级别禁用
                    case -2: $error = '密码错误!'; break;
                    default: $error = sprintf("未知错误!(%s)", $uid); break; // 0-接口参数错误（调试阶段使用）
                }
                $this->error($error);
            }
        } else { //显示登录表单
            $this->display();
        }
    }

    public function logout() {
        $config = \Common\Common\Config::getInstance();
        if ($config->hasLogin()) {
            unset($_SESSION['username']);
            unset($_SESSION['permission']);
            session_destroy();

            redirect(U('Content/Login/index'));
        } else {
            redirect(U('Content/Index/index'));
        }
    }

}