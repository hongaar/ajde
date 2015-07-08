<?php

class Config
{
	const STAGE_DEV = 'dev';
	const STAGE_LIVE = 'live';
	const STAGE_AUTO = 'auto';
	
	// Redirect this class to the following config stage
	// Default is 'auto' (chooses between DEV_STAGE and LIVE_STAGE based on remote_addrr
	public static $stage			= self::STAGE_AUTO;

	// localhost and private networks, add your own dev machine if not in
	// private network range!
	// @see http://en.wikipedia.org/wiki/Private_network
	public static $local			= array(
		'/::1/',
		'/127\.0\.0\.1/',
		'/10\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/',
		'/172\.[1-3][0-9]\.[0-9]{1,3}\.[0-9]{1,3}/',
		'/192\.168\.[0-9]{1,3}\.[0-9]{1,3}/',
        '/109\.237\.220\.206/'
	);

    /**
     *
     * @param string $stage
     * @return Config_Application
     */
	public static function getInstance($stage = null) {
		$stage = self::_getStage($stage);
		static $instance = array();
		if (!isset($instance[$stage])) {
			$className = "Config_".ucfirst($stage);
			require_once Ajde_Core_Autoloader::getFile(CONFIG_DIR, $className . '.php');
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
     * @param string $stage
     * @return mixed
     * @throws Ajde_Exception
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

    /**
     *
     * @param string $param
     * @param mixed $value
     * @return mixed
     * @throws Ajde_Exception
     */
    public static function set($param, $value) {
        $instance = self::getInstance();
        $instance->$param = $value;
    }
	
	public static function getAll($stage = null)
	{
		$stage = self::_getStage($stage);
		$instance = self::getInstance($stage);
		$vars = get_object_vars ($instance);
		return $vars;
	}
	
	public static function getStage()
	{
		return self::_getStage();
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
		if (preg_match($pattern, $_SERVER['SERVER_ADDR'])) {
			return self::STAGE_DEV;
		}
		return self::STAGE_LIVE;
	}
}