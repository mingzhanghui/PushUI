<?php
namespace Permission\Controller;
use Think\Controller;

use Common\Common\Config;

class IndexController extends Controller {
    public function __construct() {
        parent::__construct();
        // permission
        session_start();
        $config = Config::getInstance();
        if ($config->hasLogin()) {
            if (!isset($_SESSION['username']) || !$_SESSION['content']) {
                redirect(U('Permission/Login/index').'?refer='.U());
            }
        } else {
            $_SESSION['username'] = 'Guest';
            $_SESSION['permission'] = 1;
        }
    }

    public function index() {
        $this->display();
    }

    /**
     * 用户权限列表
     */
    public function listUsers() {
        $list = M('User')->select();
        $this->ajaxReturn( $list );
    }

    /**
     * 删除用户
     */
    public function delUser() {
        $uid = I('get.uid');
        if ($uid === '') {
            E(__METHOD__ . ': uid is not set!');
        }
        $res = M('user')->where(array('uid' => $uid))->delete();
        M()->execute('vacuum');
        $this->ajaxReturn($res);
    }

    /**
     * 添加用户
     */
    public function addUser() {
        $result = array('code'=>0, 'msg'=>'');

        $User = M('user');

        $username = trim( I('post.yonghuming') );
        $pwd = I('post.mima');
        $repwd = I('post.remima');

        if ($username === '') {
            $result['code'] = 1;
            $result['msg'] = '用户名不能为空';
        } else {
            if (! preg_match( '/^[A-Za-z][A-Za-z0-9]{4,19}/', $username ) ) {
                $result['code'] = 1;
                $result['msg'] = '用户名英文字母或数字, 以字母开头5-20位.';
                $this->ajaxReturn( $result );
            }

            $where = array('yonghuming' => $username);
            $t = $User->field('yonghuming')->where($where)->limit(0,1)->select();
            if (!is_null($t[0]['yonghuming'])) {
                $result['code'] = 1;
                $result['msg'] = '用户名已存在';
                $this->ajaxReturn( $result );
            }
        }

        if ($pwd !== $repwd) {
            $result['code'] = 3;
            $result['msg'] = '两次密码不一致';
            $this->ajaxReturn( $result );
        }
        if (strlen($pwd) < 5) {
            $result['code'] = 2;
            $result['msg'] = '密码长度至少5位';
            $this->ajaxReturn( $result );
        }

        $data = array(
            'yonghuming'  => $username,
            'mima'        => \Permission\Common\Hash::create('sha1', $pwd, C('HASH_PASSWORD_KEY')),
            'content'     => 1,
        );
        $level = I('post.level');
        switch($level) {
            case 1:
                $data['super'] = 1;  // 超级管理员
                $data['advertise'] = 1;
                $data['verifier'] = 1;
                break;
            case 2:
                $data['super'] = 2;   // 审核管理员
                $data['verifier'] = 1;
                break;
            default:
        }

        $t = $User->data($data)->add();
        if ($t > 0) {
            $result['code'] = 0;
            $result['msg'] = '添加用户成功';
        } else {
            $result['code'] = -1;
            $result['msg'] = '添加用户失败';
        }

        $this->ajaxReturn($result);
    }

    /**
     * 修改密码
     */
    public function modPwd() {
        $result = array('code'=>0, 'msg'=>'');

        $uid = I('post.uid');
        $pwd = I('post.pwd');
        $npwd = I('post.npwd');
        $nrepwd = I('post.nrepwd');
        if ($npwd != $nrepwd) {
            $result['code'] = 3;
            $result['msg'] = '2次密码不一致';
            $this->ajaxReturn( $result );
        }
        // 原始密码密文
        $crypt = \Permission\Common\Hash::create(
            'sha1',
            $pwd,
            C('HASH_PASSWORD_KEY')
        );

        $User = M('User');
        $where = array('uid' => $uid);
        if (strlen($npwd) < 5) {
            $result['code'] = 2;
            $result['msg'] = '密码长度不能小于5';
            $this->ajaxReturn( $result );

        } else {
            $t = $User->where($where)->field('mima')->limit(0,1)->select();
            if ($t[0]['mima'] !== $crypt) {
                $result['code'] = 1;
                $result['msg'] = '原始密码错误';
                $this->ajaxReturn( $result );
            }
        }

        $data = array('mima' => \Permission\Common\Hash::create(
            'sha1',
            $npwd,
            C('HASH_PASSWORD_KEY')
        ));
        $n = $User->where($where)->data($data)->save();
        if ($n > 0) {
            $result['code'] = 0;
            $result['msg'] = '修改密码成功';
        } else {
            $result['code'] = -1;
            $result['msg'] = '修改密码失败';
        }

        $this->ajaxReturn( $result );
    }

    /**
     * 修改权限
     */
    public function chMod() {
        $names = array('pushcontrol','content','subscribe','advertise','customer','verifier');
        $data = array();
        $cnt = 0;
        foreach ( $names as $name ) {
            if (isset($_GET[$name])) {
                $data[$name] = $_GET[$name];
                $cnt++;
            }
        }
        if (0 === $cnt) {
            E(__METHOD__ . ': unexpected field');
        }
        $uid = I('get.uid');
        if (!isset($uid)) {
           E(__METHOD__ . ': uid is not set');
        }
        $where = array('uid' => $uid);
        $t = M('user')->where( $where )->data($data)->save();

        if ($t > 0) {
            $this->ajaxReturn(array('code'=>0,'msg'=>'权限修改成功!'));
        } else {
            $this->ajaxReturn(array('code'=>1,'msg'=>'权限修改失败!'));
        }
    }

    public function test() {
        // $pwd = 'admin'; echo \Permission\Common\Hash::create('sha1', $pwd, C('HASH_PASSWORD_KEY'));

    }
}