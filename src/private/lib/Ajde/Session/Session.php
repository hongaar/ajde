<?php

class Ajde_Session extends Ajde_Object_Standard
{
	protected $_namespace = null;
	
	public function __bootstrap()
	{
		// Session name
		session_name(Config::get('ident') . '_session');
		
		// Security
		ini_set('session.gc_maxlifetime', Config::get("gcLifetime") * 60); // PHP session garbage collection timeout in minutes
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 1); // @see http://www.php.net/manual/en/session.configuration.php#ini.session.use-only-cookies
				
		// Cookie parameter
		$lifetime	= Config::get("cookieLifetime");
		$path		= Config::get('site_path');
		$domain		= Config::get('cookieDomain');
		$secure		= Config::get('cookieSecure');
		$httponly	= Config::get('cookieHttponly');		
		
		session_set_cookie_params($lifetime * 60, $path, $domain, $secure, $httponly);
		session_cache_limiter('private_no_expire');
		
		// Start the session!
		session_start();
		
		// Force send new cookie with updated lifetime (forcing keep-alive)
		// @see http://www.php.net/manual/en/function.session-set-cookie-params.php#100672
		session_regenerate_id();
		
		// Strengthen session security with REMOTE_ADDR and HTTP_USER_AGENT
		// @see http://shiflett.org/articles/session-hijacking
		if (isset($_SESSION['client']) &&
				$_SESSION['client'] !== md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . Config::get('secret'))) {
			session_regenerate_id();
			session_destroy();
			// TODO:
			$exception = new Ajde_Exception('Possible session hijacking detected. Bailing out.');
			if (Config::getInstance()->debug === true) {
				throw $exception;
			} else {
				Ajde_Exception_Log::logException($exception);	
				Ajde_Http_Response::dieOnCode(Ajde_Http_Response::RESPONSE_TYPE_FORBIDDEN);
			}
		} else {
			$_SESSION['client'] = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . Config::get('secret'));
		}
		
		// remove cache headers invoked by session_start();
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			header_remove('X-Powered-By');
		}
		return true;
	}
	
	public function __construct($namespace = 'default')
	{
		$this->_namespace = $namespace;
	}
	
	public function destroy($key = null)
	{
		if (isset($key)) {
			if ($this->has($key)) {
				$_SESSION[$this->_namespace][$key] = null;
			}
		} else {
			$_SESSION[$this->_namespace] = null;
			$this->reset(); 
		}
	}
	
	public function setModel($name, $object)
	{
		$this->set($name, serialize($object));	
	}
	
	public function getModel($name)
	{
		// If during the session class definitions has changed, this will throw an exception.
		try {
			return unserialize($this->get($name));
		} catch(Exception $e) {
			Ajde_Dump::warn('Model definition changed during session');
			return false;
		}
	}
	
	public function has($key)
	{
		if (!isset($this->_data[$key]) && isset($_SESSION[$this->_namespace][$key])) {
			$this->set($key, $_SESSION[$this->_namespace][$key]);
		}
		return parent::has($key);
	}
	
	public function set($key, $value)
	{
		parent::set($key, $value);
		if ($value instanceof Ajde_Model) {
			// TODO:
			throw new Ajde_Exception('It is not allowed to store a Model directly in the session, use Ajde_Session::setModel() instead.');
		}
		$_SESSION[$this->_namespace][$key] = $value;
	}
	
	public function getOnce($key)
	{
		$return = $this->get($key);
		$this->set($key, null);
		return $return;
	}
}