<?php

abstract class Ajde_User extends Ajde_Model
{	
	protected $_autoloadParents = false;
	protected $_displayField = 'fullname';
	
	public $usernameField = 'username';
	public $passwordField = 'password';
	
	const USERGROUP_USERS	= 1;
	const USERGROUP_ADMINS	= 2;
	
	public $defaultUserGroup = self::USERGROUP_USERS;
	
	protected $cookieLifetime = 30; // in days
	
	/**
	 *
	 * @return UserModel 
	 */
	public static function getLoggedIn()
	{
		$session = new Ajde_Session('user');
		if ($session->has('model')) {
			$user = $session->getModel('model');
			return $user;
		} else {
			return false;
		}
	}
	
	public function loadByCredentials($username, $password)
	{
		$sql = 'SELECT * FROM '.$this->_table.' WHERE '.$this->usernameField.' = ? LIMIT 1';
		$values = array($username);
		$user = $this->_load($sql, $values);
		if ($user === false) {
			return false;
		}
		return ($this->verifyHash($password) ? $user : false);
	}
	
	public function createHash($password)
	{
		// @see http://net.tutsplus.com/tutorials/php/understanding-hash-functions-and-keeping-passwords-safe/
		if (CRYPT_BLOWFISH !== 1) {
			Ajde_Dump::warn('BLOWFISH algorithm not available for hashing, using MD5 instead');
			// Use MD5
			$algo = '$1';
			$cost = '';
			$unique_salt = $this->generateSecret(12);
		} else {
			// Use BLOWFISH
			$algo = '$2a';
			$cost = '$10';
			$unique_salt = $this->generateSecret(22);
		}		
		$hash = crypt($password, $algo . $cost . '$' . $unique_salt);
		if (empty($hash)) {
			// TODO:
			throw new Ajde_Exception('crypt() algorithm failed');
		}
		return $hash;
	}
	
	public function verifyHash($password)
	{
		$hash = $this->get($this->passwordField);
		if (empty($hash)) {
			return false;
		}
		if (CRYPT_BLOWFISH !== 1) {
			// Use MD5
			$full_salt = substr($hash, 0, 15);
		} else {
			// Use BLOWFISH
			$full_salt = substr($hash, 0, 29);
		}
		$new_hash = crypt($password, $full_salt);
		return ($hash == $new_hash);
	}
	
	public function login()
	{
		if (empty($this->_data)) {
			// TODO:
			throw new Ajde_Exception('Invalid user object');
		}
		$session = new Ajde_Session('user');
		$session->setModel('model', $this);
	}
	
	public function logout()
	{
		// First destroy current session
		session_regenerate_id();
		$session = new Ajde_Session('user');
		$session->destroy();
		$cookie = new Ajde_Cookie(Config::get('ident') . '_user');
		$cookie->destroy();
	}
	
	public function refresh() 
	{
		$this->loadByPK($this->getPK());
	}
	
	protected function generateSecret($length = 255)
	{
		return substr(sha1(mt_rand()), 0, $length);
	}
	
	public function add($username, $password)
	{
		$hash = $this->createHash($password);
		$this->populate(array(
			$this->usernameField	=> $username,
			$this->passwordField	=> $hash,
			'secret'				=> $this->generateSecret()
		));			
		return $this->insert();
	}
	
	public function storeCookie()
	{
		$hash = $this->getCookieHash();		
		$cookieValue = $this->getPK() . ':' . $hash;
		$cookie = new Ajde_Cookie(Config::get('ident') . '_user');
		$cookie->setLifetime($this->cookieLifetime);
		$cookie->set('auth', $cookieValue);
		return true;
	}
	
	private function getCookieHash()
	{
		if (empty($this->_data)) {
			// TODO:
			throw new Ajde_Exception('Invalid user object');
		}
		if (!in_array('sha256', hash_algos())) {
			// TODO:
			throw new Ajde_Exception('SHA-256 algorithm not available for hashing');
		}
		$userSecret	= $this->get('secret');
		$appSecret	= Config::get('secret');
		$hash = hash("sha256", $userSecret . $appSecret . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
		if (empty($hash)) {
			// TODO:
			throw new Ajde_Exception('SHA-256 algorithm failed');
		}
		return $hash;
	}
	
	public function verifyCookie()
	{
		$cookie = new Ajde_Cookie(Config::get('ident') . '_user');
		if (!$cookie->has('auth')) {
			return false;
		}
		$auth = $cookie->get('auth');
		list($uid, $hash) = explode(':', $auth);
		if (!$this->loadByPK($uid)) {
			return false;
		}
		if ($this->getCookieHash() === $hash) {
			$this->login();
			Ajde_Session_Flash::alert(sprintf(__('Welcome back %s, we automatically logged you in.'), $this->getFullname()));
		} else {
			return false;
		}
	}
	
	public function checkChangeEmail($newEmail)
	{
		$values = array($newEmail, $this->getPK());
		$sql = 'SELECT * FROM '.$this->_table.' WHERE email = ? AND id != ? LIMIT 1';
		return $this->_load($sql, $values);
	}
}