<?php

abstract class Ajde_User extends Ajde_Model
{	
	protected $_autoloadParents = false;
	protected $_displayField = 'fullname';
	
	public $usernameField = 'username';
	public $passwordField = 'password';
	
	const USERGROUP_USERS		= 1;
	const USERGROUP_ADMINS		= 2;
	const USERGROUP_CLIENTS		= 3;
	const USERGROUP_EMPLOYEES	= 4;
	
	public $defaultUserGroup = self::USERGROUP_USERS;
	
	protected $cookieLifetime = 30; // in days
	
	private static $_user;
	
	/**
	 *
	 * @return UserModel 
	 */
	public static function getLoggedIn()
	{
		if (!isset(self::$_user)) {
			$session = new Ajde_Session('user');
			if ($session->has('model')) {
				$user = $session->getModel('model');
				self::$_user = $user;
			} else {
				self::$_user = false;
			}
		}
		return self::$_user;
	}
	
	public static function isAdmin()
	{
		return ((string) self::getLoggedIn()->getUsergroup() == self::USERGROUP_ADMINS);
	}

    public static function isDebugger()
    {
        return ( ($user = self::getLoggedIn()) && $user->getDebug() );
    }

    public static function isTester()
    {
        return ( ($user = self::getLoggedIn()) && $user->getTester() );
    }

    public function hasPassword()
    {
        return !$this->verifyHash('');
    }
	
	public function loadByCredentials($username, $password)
	{
        if (empty($username) || empty($password)) {
            return false;
        }

		$sql = 'SELECT * FROM '.$this->_table.' WHERE '.$this->usernameField.' = ? LIMIT 1';
		$values = array($username);
		$user = $this->_load($sql, $values);
		if ($user === false) {
			return false;
		}
		return ($this->verifyHash($password) ? $user : false);
	}
	
	/**
	 * 
	 * @return UsergroupModel:
	 */
	public function getUsergroup()
	{
		$this->loadParent('usergroup');
		return $this->get('usergroup');
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
		self::$_user = $this;
	}
	
	public function logout()
	{
		// First destroy current session
		// TODO: overhead to call session_regenerate_id? is it not required??
		//session_regenerate_id();
		$session = new Ajde_Session('user');
		$session->destroy();
		$cookie = new Ajde_Cookie(Config::get('ident') . '_user');
		$cookie->destroy();
		self::$_user = null;
	}
	
	public function refresh() 
	{
		$this->loadByPK($this->getPK());
	}
	
	public function generateSecret($length = 255)
	{
		return substr(sha1(mt_rand()), 0, $length);
	}
	
	public function add($username, $password)
	{
		$hash = $this->createHash($password);
		$this->populate(array(
			$this->usernameField	=> $username,
			$this->passwordField	=> $hash,
			'usergroup'				=> $this->defaultUserGroup,
			'secret'				=> $this->generateSecret()
		));			
		return $this->insert();
	}
	
	public function storeCookie($includeDomain = true)
	{
		$hash = $this->getCookieHash($includeDomain);		
		$cookieValue = $this->getPK() . ':' . $hash;
		$cookie = new Ajde_Cookie(Config::get('ident') . '_user', true);
		$cookie->setLifetime($this->cookieLifetime);
		$cookie->set('auth', $cookieValue);
		return true;
	}
	
	public function getCookieHash($includeDomain = true)
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
		if ($includeDomain) {
			$hash = hash("sha256", $userSecret . $appSecret . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
		} else {
			$hash = hash("sha256", $userSecret . $appSecret);
		}
		if (empty($hash)) {
			// TODO:
			throw new Ajde_Exception('SHA-256 algorithm failed');
		}
		return $hash;
	}
	
	public function verifyCookie($includeDomain = true)
	{
		$cookie = new Ajde_Cookie(Config::get('ident') . '_user', true);
		if (!$cookie->has('auth')) {
			return false;
		}
		$auth = $cookie->get('auth');
		list($uid, $hash) = explode(':', $auth);
		if (!$this->loadByPK($uid)) {
			return false;
		}
		if ($this->getCookieHash($includeDomain) === $hash) {
			$this->login();
			Ajde_Session_Flash::alert(sprintf(__('Welcome back %s'), $this->getFullname()));
            Ajde_Cache::getInstance()->disable();
		} else {

			return false;
		}
	}
	
	public function canChangeEmailTo($newEmail)
	{
		if ($this->isFieldEncrypted('email')) {
			$newEmail = $this->doEncrypt($newEmail);
		}
		$values = array($newEmail, $this->getPK());
		$sql = 'SELECT * FROM '.$this->_table.' WHERE email = ? AND id != ? LIMIT 1';
		return !$this->_load($sql, $values, false);
	}
    
    public function canChangeUsernameTo($newUsername)
	{
		if ($this->isFieldEncrypted($this->usernameField)) {
			$newUsername = $this->doEncrypt($newUsername);
		}
		$values = array($newUsername, $this->getPK());
		$sql = 'SELECT * FROM '.$this->_table.' WHERE ' . $this->usernameField . ' = ? AND id != ? LIMIT 1';
		return !$this->_load($sql, $values, false);
	}
	
	public function resetUser()
	{
		if (!$this->hasNotEmpty('email')) {
			return false;
		}
		$resetHash = $this->getResetHash();
		$this->set('reset_hash', $resetHash);
		$this->save();
		$this->sendResetMail($resetHash);
		
		return $resetHash;
	}
	
	public function sendResetMail($hash)
	{
		// @todo exception
		throw new Ajde_Exception('Please implement sendResetMail in UserModel');
		return false;
	}
	
	public function getResetHash()
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
		$hash = strtotime("+1 month") . ':' . hash("sha256", $userSecret . $appSecret . microtime() . rand());

		if (empty($hash)) {
			// TODO:
			throw new Ajde_Exception('SHA-256 algorithm failed');
		}
		return $hash;
	}
}