<?php

class Config {

	// Redirect this class to the following config stage
	// Default is 'auto' (chooses between 'dev' and 'live' based on remote_addrr
	public static $stage			= 'auto';
	
	// localhost and private networks, add your own dev machine if not in
	// private network range!
	// @see http://en.wikipedia.org/wiki/Private_network
	public static $local			= array(
		'/127\.0\.0\.1/',
		'/10\.[1-9]{1,3}\.[1-9]{1,3}\.[1-9]{1,3}/',
		'/172\.[1-3][0-9]\.[1-9]{1,3}\.[1-9]{1,3}/',
		'/192\.168\.[1-9]{1,3}\.[1-9]{1,3}/'
	);

	/**
	 * 
	 * @return Config_Application
	 */
	public static function getInstance($stage = null) {
		$stage = self::_getStage($stage);
		static $instance = array();
		if (!isset($instance[$stage])) {
			$className = "Config_".ucfirst($stage);
			require_once $className . '.php';
			if (class_exists($className)) {
				$instance[$stage] = new $className();
			} else {
				$exceptionClass = class_exists('Ajde_Core_Autoloader_Exception') ? 'Ajde_Core_Autoloader_Exception' : 'Exception';
				throw new $exceptionClass("Unable to load $className", 90005);
			}
			
		}
		return $instance[$stage];
	}
	
	/**
	 * 
	 * @param string $param
	 * @return mixed
	 */
	public static function get($param, $stage = null) {
		$stage = self::_getStage($stage);
		$instance = self::getInstance($stage);
		if (isset($instance->$param)) {
			return $instance->$param;
		} else {
			throw new Ajde_Exception("Config parameter $param not set", 90004);
		}
	}

	private static function _getStage($stage = null) {
		$stage = empty($stage) ? self::$stage : $stage;
		if (strtolower($stage) === 'auto') {
			$stage = self::_getAutoStage();
		}
		return $stage;
	}
		
	private static function _getAutoStage()
	{
		foreach(self::$local as $pattern)
		if (preg_match($pattern, $_SERVER['REMOTE_ADDR'])) {
			return 'dev';
		}
		return 'live';
	}
}