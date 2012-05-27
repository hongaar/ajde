<?php

class Ajde_Http_Request extends Ajde_Object_Standard
{
	const TYPE_STRING 	= 1;
	const TYPE_HTML 	= 2;
	const TYPE_INTEGER 	= 3;
	const TYPE_FLOAT 	= 4;
	const TYPE_RAW	 	= 5;
	
	const FORM_MIN_TIME	= 1; 	// minimum time to have a post form returned (seconds)
	const FORM_MAX_TIME	= 3600;	// timeout of post forms (seconds) 
	
	/**
	 * @var Ajde_Core_Route
	 */
	protected $_route = null;
	protected $_postData = array();
	
	/**
	 * @return Ajde_Http_Request
	 */
	public static function fromGlobal()
	{
		$instance = new self();
		if (!empty($_POST) && self::requirePostToken() && !self::_isWhitelisted()) {
			
			// Measures against CSRF attacks
			$session = new Ajde_Session('AC.Form');
			if (!isset($_POST['_token']) || !$session->has('formTime')) {
				// TODO:
				$exception = new Ajde_Exception('No form token received or no form time set, bailing out to prevent CSRF attack');
				if (Config::getInstance()->debug === true) {
					Ajde_Http_Response::setResponseType(Ajde_Http_Response::RESPONSE_TYPE_FORBIDDEN);
					throw $exception;
				} else {
					// Prevent inf. loops
					unset($_POST);
					// Rewrite
					Ajde_Exception_Log::logException($exception);	
					Ajde_Http_Response::dieOnCode(Ajde_Http_Response::RESPONSE_TYPE_FORBIDDEN);
				}
			}
			$formToken = $_POST['_token'];
			if (!self::verifyFormToken($formToken) || !self::verifyFormTime()) {
				// TODO:
				if (!self::verifyFormToken($formToken)) {
					$exception = new Ajde_Exception('No matching form token (got ' . self::_getHashFromSession($formToken) . ', expected ' . self::_tokenHash($formToken) . '), bailing out to prevent CSRF attack');
				} else {
					$exception = new Ajde_Exception('Form token timed out, bailing out to prevent CSRF attack');
				}
				if (Config::getInstance()->debug === true) {
					Ajde_Http_Response::setResponseType(Ajde_Http_Response::RESPONSE_TYPE_FORBIDDEN);
					throw $exception;
				} else {
					// Prevent inf. loops
					unset($_POST);
					// Rewrite
					Ajde_Exception_Log::logException($exception);	
					Ajde_Http_Response::dieOnCode(Ajde_Http_Response::RESPONSE_TYPE_FORBIDDEN);
				}
			}
		}
		// Security measure, protect $_POST
		//$global = array_merge($_GET, $_POST);
		$global = $_GET;
		foreach($global as $key => $value)
		{
			$instance->set($key, $value);
		}		
		$instance->_postData = $_POST;
		if (!empty($instance->_postData)) {
			Ajde_Cache::getInstance()->disable();
		}
		return $instance;
	}

	public static function getRefferer()
	{
		return $_SERVER['HTTP_REFERER'];
	}
	
	/**
	 * Security
	 */
	private static function autoEscapeString()
	{
		return Config::getInstance()->autoEscapeString == true;
	}
	
	private static function autoCleanHtml()
	{
		return Config::getInstance()->autoCleanHtml == true;
	}
	
	private static function requirePostToken()
	{
		return Config::getInstance()->requirePostToken == true;
	}
	
	/**
	 * CSRF prevention token
	 */
	public static function getFormToken()
	{
		static $token;
		if (!isset($token)) {
			Ajde_Cache::getInstance()->disable();
			$token = md5(uniqid(rand(), true));
			$session = new Ajde_Session('AC.Form');
			$tokenDictionary = self::_getTokenDictionary($session);
			$tokenDictionary[$token] = self::_tokenHash($token);
			$session->set('formTokens', $tokenDictionary);
		}
		self::markFormTime();
		return $token;
	}
	
	public static function verifyFormToken($requestToken)
	{
		return (self::_tokenHash($requestToken) === self::_getHashFromSession($requestToken));
	}
	
	private static function _tokenHash($token)
	{
		return md5($token . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . Config::get('secret'));
	}
	
	private static function _isWhitelisted()
	{
		$route = issetor($_GET['_route'], false);
		foreach(Config::get('postWhitelistRoutes') as $whitelist) {
			if (stripos($route, $whitelist) === 0) {
				return true;
			}
		}
		return false;
	}
	
	private static function _getTokenDictionary(&$session = null)
	{
		if (!isset($session)) {
			$session = new Ajde_Session('AC.Form');
		}
		$tokenDictionary = ($session->has('formTokens') ? $session->get('formTokens') : array());
		if (!is_array($tokenDictionary)) {
			$tokenDictionary = array();
		}
		return $tokenDictionary;
	}
	
	private static function _getHashFromSession($token)
	{
		$tokenDictionary = self::_getTokenDictionary();
		return (issetor($tokenDictionary[$token], ''));		
	}
	
	public static function markFormTime()
	{
		$time = time();
		$session = new Ajde_Session('AC.Form');
		$session->set('formTime', $time);
		return $time;
	}
	
	public static function verifyFormTime()
	{
		$session = new Ajde_Session('AC.Form');
		$sessionTime = $session->get('formTime');
		if ((time() - $sessionTime) < self::FORM_MIN_TIME ||
			(time() - $sessionTime) > self::FORM_MAX_TIME) {
			return false;
		} else {
			return true;
		}
	}
	
	public static function isAjax()
	{
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}
	
	/**
	 * Helpers
	 */
	public function get($key)
	{
		return $this->getParam($key);
	}
	
	public function getParam($key, $default = null, $type = self::TYPE_STRING, $post = false)
	{
		$data = $this->_data;
		if ($post === true) {
			$data = $this->getPostData();
		}
		if (isset($data[$key])) {
			switch ($type) {
				case self::TYPE_HTML:
					if ($this->autoCleanHtml() === true) {
						return Ajde_Component_String::clean($data[$key]);
					} else {
						return $data[$key];
					}
					break;
				case self::TYPE_INTEGER:
					return (int) $data[$key];
					break;
				case self::TYPE_FLOAT:
					return (float) $data[$key];
					break;
				case self::TYPE_RAW:
					return $data[$key];
					break;
				case self::TYPE_STRING:
				default:
					if ($this->autoEscapeString() === true) {
						if (is_array($data[$key])) {
							array_walk($data[$key], array("Ajde_Component_String", "escape"));
							return $data[$key];
						} else {
							return Ajde_Component_String::escape($data[$key]);
						}
					} else {
						return $data[$key];
					}
			}			
		} else {
			if (isset($default)) {
				return $default;
			} else {
				// TODO:
				throw new Ajde_Exception("Parameter '$key' not present in request and no default value given");
			}
		}
	}
	
	public function getStr($key, $default)	{ return $this->getString	($key, $default); }
	public function getInt($key, $default)	{ return $this->getInteger	($key, $default); }
	
	public function getString($key, $default = null)
	{
		return $this->getParam($key, $default, self::TYPE_STRING);
	}
	
	public function getHtml($key, $default = null)
	{
		return $this->getParam($key, $default, self::TYPE_HTML);
	}
	
	public function getInteger($key, $default = null)
	{
		return $this->getParam($key, $default, self::TYPE_INTEGER);
	}
	
	public function getFloat($key, $default = null)
	{
		return $this->getParam($key, $default, self::TYPE_FLOAT);
	}
	
	public function getRaw($key, $default = null)
	{
		return $this->getParam($key, $default, self::TYPE_RAW);
	}
	
	/**
	 * FORM
	 */
	
	public function getCheckbox($key, $post = true)
	{
		if ($this->getParam($key, false, self::TYPE_RAW, $post) ===  'on') {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * POST
	 */
	
	public function getPostData()
	{
		return $this->_postData;
	}
	
	public function getPostParam($key, $default = null, $type = self::TYPE_STRING)
	{
		return $this->getParam($key, $default, $type, true);
	}
	
	public function hasPostParam($key)
	{
		return array_key_exists($key, $this->_postData);
	}
	
	/**
	 * @return Ajde_Core_Route
	 */
	public function getRoute()
	{
		if (!isset($this->_route))
		{
			$routeKey = '_route';
			if (!$this->has($routeKey)) {
				$this->set($routeKey, false);
			}
			$this->_route = new Ajde_Core_Route($this->getRaw($routeKey));
			foreach ($this->_route->values() as $part => $value) {
				$this->set($part, $value);
			}
		}
		return $this->_route;
	}
	
	public function initRoute()
	{
		$route = $this->getRoute();
		$langInstance = Ajde_Lang::getInstance();
		if ($route->hasLang()) {			
			$langInstance->setGlobalLang($route->getLang());
		}
		return $route;
	}

}