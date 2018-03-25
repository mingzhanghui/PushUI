<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-15
 * Time: 16:06
 */
namespace Permission\Controller;
use Think\Controller;

class LoginController extends Controller {
    public function index($refer='') {
        // unset($_SESSION); session_destroy(); die;
        session_start();
        if (isset($_SESSION['username']) && $_SESSION['permission']) {
            $refer = I('get.refer');
            $refer = ($refer === '') ? U('Permission/Index/index') : urldecode($refer);
            redirect($refer);
        }

        if(IS_POST) { //登录验证
            $username = I('post.username');
            $password = I('post.password');
            $User = new \Permission\Model\UserModel();
            $uid = $User->login($username, $password); //获取数据库中保存的用户ID

            if(0 < $uid) { // 登录成功
                // 判断进入权限管理系统的权限
                if ( !$User->permission($uid, 'permission') ) {
                    $this->error($username.'没有权限访问权限管理');
                }

                // 将用户名存入session
                session('username', $username);
                session('permission', 1);

                // 跳转到登录前页面
                $refer = I('get.refer');
                $refer = ($refer === '') ? U('Permission/Index/index') : urldecode($refer);
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

            redirect(U('Permission/Login/index'));
        } else {
            redirect(U('Permission/Index/index'));
        }
    }

}