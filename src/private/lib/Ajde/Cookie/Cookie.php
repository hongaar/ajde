<?php

class Ajde_Cookie extends Ajde_Object_Standard
{
	protected $_namespace = null;
	protected $_lifetime = 90;
	
	public function __construct($namespace = 'default')
	{
		$this->_namespace = $namespace;
		if (isset($_COOKIE[$this->_namespace])) {
			$this->_data = unserialize($_COOKIE[$this->_namespace]);
		}
	}
	
	public function destroy()
	{
		$this->_setcookie('', time() - 3600);
		$this->reset(); 
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
			Ajde_Dump::warn('Model definition changed during cookie period');
			return false;
		}
	}
	
	public function setLifetime($days)
	{
		$this->_lifetime = $days;
	}
		
	public function set($key, $value)
	{
		parent::set($key, $value);
		if ($value instanceof Ajde_Model) {
			// TODO:
			throw new Ajde_Exception('It is not allowed to store a Model directly in a cookie, use Ajde_Cookie::setModel() instead.');
		}
		$this->_setcookie(serialize($this->values()), time() + (60 * 60 * 24 * $this->_lifetime));
	}
	
	protected function _setcookie($value, $lifetime)
	{
		$path		= Config::get('site_path');
		$domain		= Config::get('cookieDomain');
		$secure		= Config::get('cookieSecure');
		$httponly	= Config::get('cookieHttponly');
		setcookie($this->_namespace, $value, $lifetime, $path, $domain, $secure, $httponly);
	}
}