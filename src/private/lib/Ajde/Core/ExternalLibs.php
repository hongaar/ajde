<?php

class Ajde_Core_ExternalLibs extends Ajde_Object_Singleton
{
	/**
	 *
	 * @staticvar Ajde_Application $instance
	 * @return Ajde_Application
	 */
	public static function getInstance()
	{
		static $instance;
		return $instance === null ? $instance = new self : $instance;
	}
	
	public function __bootstrap()
	{
		if (file_exists(LIB_DIR . 'HTMLPurifier/HTMLPurifier.auto.php')) {							
			require_once LIB_DIR . 'HTMLPurifier/HTMLPurifier.auto.php';
			// Optional components still need to be included--you'll know if you try to
			// use a feature and you get a class doesn't exists error! The autoloader
			// can be used in conjunction with this approach to catch classes that are
			// missing. Simply uncomment the next line:
			// require_once LIB_DIR . 'HTMLPurifier/HTMLPurifier.autoload.php';
			$purifier = new HTMLPurifier();
			$this->set('HTMLPurifier', $purifier);
		}
		return true;
	}
	
	public static function getClassname($className)
	{
		$config = Config::getInstance();
		return (isset($config->overrideClass[$className])) ? 
			$config->overrideClass[$className] :
			$className;
	}
	
	public static function setOverride($className, $newClass)
	{
		$config = Config::getInstance();
		$config->overrideClass[$className] = $newClass;
	}

}