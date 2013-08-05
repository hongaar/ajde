<?php

class Ajde_Session extends Ajde_Object_Standard
{
	protected $_namespace = null;
	
	public function __bootstrap()
	{
		// Session name
		session_name(Config::get('ident') . '_session');		
		
		// Session lifetime
		$lifetime	= Config::get("sessionLifetime");
		
		// Security
		ini_set('session.gc_maxlifetime', ($lifetime == 0 ? 180 * 60 : $lifetime * 60)); // PHP session garbage collection timeout in minutes
		ini_set('session.gc_divisor', 100);		// Set divisor and probability for cronjob Ubuntu/Debian
		ini_set('session.gc_probability', 1);	// @see http://www.php.net/manual/en/function.session-save-path.php#98106
		if ( Config::get('sessionSavepath') ) { 
			ini_set('session.save_path', str_replace('~', Config::get('local_root'), Config::get('sessionSavepath')));
		}
		ini_set('session.use_cookies', 1);
		ini_set('session.use_only_cookies', 1); // @see http://www.php.net/manual/en/session.configuration.php#ini.session.use-only-cookies
				
		// Session cookie parameter		
		$path		= Config::get('site_path');
		$domain		= Config::get('cookieDomain');
		$secure		= Config::get('cookieSecure');
		$httponly	= Config::get('cookieHttponly');		
		
		session_set_cookie_params($lifetime * 60, $path, $domain, $secure, $httponly);
		session_cache_limiter('private_no_expire');
				
		// Start the session!
		session_start();
				
		if ($lifetime > 0) {
			// Force send new cookie with updated lifetime (forcing keep-alive)
			// @see http://www.php.net/manual/en/function.session-set-cookie-params.php#100672
			//session_regenerate_id();
			
			// Set cookie manually
			// @see http://www.php.net/manual/en/function.session-set-cookie-params.php#100657
			setcookie(session_name(), session_id(), time() + ($lifetime * 60), $path, $domain, $secure, $httponly);
		}
		
		// Strengthen session security with REMOTE_ADDR and HTTP_USER_AGENT
		// @see http://shiflett.org/articles/session-hijacking

		// Ignore Google Chrome frame as it has a split personality
		// @todo TODO: security issue!!
		// @see http://www.chromium.org/developers/how-tos/chrome-frame-getting-started/understanding-chrome-frame-user-agent		
		if (substr_count($_SERVER['HTTP_USER_AGENT'], 'chromeframe/') === 0 &&
				isset($_SESSION['client']) &&
				$_SESSION['client'] !== md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . Config::get('secret'))) {
			// TODO: overhead to call session_regenerate_id? is it not required??
			//session_regenerate_id();
			session_destroy();
			// TODO:
			$exception = new Ajde_Exception('Possible session hijacking detected. Bailing out.');
			if (Config::getInstance()->debug === true) {
				throw $exception;
			} else {
				Ajde_Exception_Log::logException($exception);
				// don't redirect for resource items, as they should have no side effect
				// this makes it possible for i.e. web crawlers/error pages to view resources
				$request = Ajde_Http_Request::fromGlobal();
				$route = $request->initRoute();	
				Ajde::app()->setRequest($request);
				if (!in_array($route->getFormat(), array('css','js'))) {	
					Ajde_Http_Response::dieOnCode(Ajde_Http_Response::RESPONSE_TYPE_FORBIDDEN);
				}
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
				$this->remove($key);
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