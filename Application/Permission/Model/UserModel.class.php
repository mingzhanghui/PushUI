<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-05-15
 * Time: 14:00
 */
namespace Permission\Model;
use Think\Model;

class UserModel extends Model {
	protected $fields = array('uid', 'yonghuming', 'mima', 'pushcontrol', 'content',
		'subscribe', 'advertise', 'customer', 'permission', 'super', 'verifier',
		'_type' => array('uid' => 'bigint', 'yonghuming' => 'varchar', 'mima' => 'varchar',
			'pushcontrol' => 'int', 'content' => 'int', 'subscribe' => 'int',
			'advertise' => 'int', 'customer' => 'int', 'permission' => 'int',
			'super' => 'int', 'verifier' => 'int',
		),
	);
	protected $pk = 'uid';

	public function __construct() {
		parent::__construct();
		$this->autoCheckFields = true;
	}

	/**
	 * GET uid by username as password
	 * @param $username
	 * @param $password
	 * @return
	 */
	public function login($username, $password) {
		// 用户名不存在
		$t = $this->field('uid')->where(array('yonghuming' => $username))->limit(0, 1)->select();
		if (is_null($t[0]['uid'])) {
			return -1;
		}
		// 密码不对
		$crypt = \Permission\Common\Hash::create('sha1', $password, C('HASH_PASSWORD_KEY'));
		$where = array(
			'yonghuming' => $username,
			'mima' => $crypt,
		);
		$t = $this->where($where)->limit(0, 1)->select();
		if (is_null($t[0]['uid'])) {
			return -2;
		}
		// success
		return $t[0]['uid'];
	}

	public function permission($uid, $field) {
		$where = array('uid' => $uid);
		$t = $this->field($field)->where($where)->limit(0, 1)->select();
		return $t[0][$field];
	}
}