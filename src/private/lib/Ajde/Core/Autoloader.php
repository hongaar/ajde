<?php

class Ajde_Core_Autoloader
{
	protected static $dirPrepend = null;
	protected static $namespaces = null;
	public static $dirs = array();
	public static $files = array();
	
	static $missed = 0;
	static $queries = 0;
	
	// These (ZF) classes could pose problems to the Ajde MVC mechanisms (?)
	public static $incompatibleClasses = array(
		'Zend_Loader_Autoloader',
		'Zend_Application',		
		'Zend_Application_Bootstrap_Bootstrap'
	);
	
	 
	public static function register($dirPrepend = null)
	{
		// Dir prepend
		self::$dirPrepend = $dirPrepend;
		
		// Zend requires include path to be set to the LIB directory
		// Include config dir here to speed up requiring the config classes
		set_include_path(get_include_path() . PATH_SEPARATOR . LIB_DIR);
		
		// Get namespaces from Config
		$defaultNamespaces = array('Ajde', 'AjdeX', 'Zend', 'HTMLPurifier');
		if (!self::exists('Config')) {			
			require_once CONFIG_DIR . 'Config.php';
		}
		
		// Try to use the Config object, and if it fails we have to redirect to
		// an error page to prevent calling the shutdown function with no Config
		// instance loaded
		try {
			$configNamespaces = Config::get('registerNamespaces');		
		} catch (Exception $e) {
			error_log($e->getMessage());
			include 'errordocument.php';
			die();
		}
		self::$namespaces = array_merge($defaultNamespaces, $configNamespaces);
		
		// Configure autoloading
		spl_autoload_register(array("Ajde_Core_Autoloader", "autoload"));
		
		// Init dirs and files
		self::initDirs();
	}
	
	public static function addDir($dir)
	{
		self::$dirs[] = $dir;
		self::$dirs = array_unique(self::$dirs);
	}
	
	public static function addFile($file, $prepend = false)
	{
		if ($prepend) {
			array_unshift(self::$files, $file);
		} else {
			self::$files[] = $file;
		}
		
		self::$files = array_unique(self::$files);
	}
	
	public static function initDirs()
	{
		// Add libraries and config to include path
		self::addDir(LIB_DIR);
		self::addDir(MODULE_DIR);
		self::addDir(CONFIG_DIR);
	}
	
	public static function initFiles($className)
	{
		// Namespace/Class.php naming
		self::addFile(str_ireplace('_', '/', $className) . ".php");

		// Ajde_Foo defaults to the next naming scheme
		$prepend = false;
		if (substr($className, 0, 4) == 'Ajde' && substr_count($className, '_') === 1) {
			$prepend = true;
		}
		
		// Namespace/Class/Class.php naming		
		$classNameArray = explode("_", $className);
		$tail = end($classNameArray);
		$head = implode("/", $classNameArray);
		self::addFile($head . "/" . $tail . ".php", $prepend);
		
		// Namespace_Class.php naming
		self::addFile($className . ".php");		
		
		// Class/Class.php naming
		self::addFile($className . '/' . $className . ".php");
	}

	public static function autoload($className)
	{
		$debug = false; // turn on for performance checking of the autoloader
		
		if (in_array($className, self::$incompatibleClasses)) {
			throw new Ajde_Exception('Could not create instance of incompatible class ' . $className . '.', 90018);
		}
		
		self::$files = array();
				
		$isNamespace = false;
		
		foreach(self::$namespaces as $namespace) {
			if (substr($className, 0, strlen($namespace . '_')) == $namespace . '_') {
				$isNamespace = true;
				break;
			}
		}
		
		if ($isNamespace) {
			// LIB class
			$dirs = array(LIB_DIR);
			self::initFiles($className);
		} else {			
			// Non LIB related classes
			if (substr_count($className, 'Controller') > 0) {
				$dirs = array(MODULE_DIR);
				
				$controllerName = str_replace('Controller', '', $className);
				if (strtolower(substr($controllerName, 1)) != substr($controllerName, 1)) { // See if we got more capitals
					// ModuleSubcontrollerController.php naming
					$combinedName = substr($controllerName, 0, 1) . preg_replace('/([A-Z])/', ':\1', substr($controllerName, 1));
					list($moduleName, $controllerName) = explode(":", $combinedName);
					self::addFile(strtolower($moduleName) . "/" . $moduleName . $controllerName . 'Controller.php');
				} else {
					// ModuleController.php naming
					self::addFile(strtolower(str_replace('Controller', '', $className)) . "/" . $className . '.php');
				}
			} elseif (substr_count($className, 'Config') > 0) {
				$dirs = array(CONFIG_DIR);
				// Namespace_Class.php naming
				self::addFile($className . ".php");
			} else {
				$dirs = self::$dirs;
				// FooModel.php, BarCollection.php, etc. naming
				self::addFile($className . '.php');
			}
		}
		
		/*// In order to use Ajde_Event here, require neccesary files statically :(
		require_once(LIB_DIR.'Ajde/Object/Object.php');
		require_once(LIB_DIR.'Ajde/Object/Static.php');
		require_once(LIB_DIR.'Ajde/Event/Event.php');
		require_once(LIB_DIR.'Ajde/Exception/Exception.php');
		Ajde_Event::trigger('Ajde_Core_Autoloader', 'beforeSearch', array($className));*/
		
		if ($debug) {
			self::$queries++;
			echo "<span style='color:orange;'>LOOKING FOR</span> $className <br/>";
		}
		foreach ($dirs as $dir) {
			foreach (self::$files as $file) {						
				$path = self::$dirPrepend.$dir.$file;
				if (is_file($path)) {
					// TODO: performance gain?
					// if (class_exists('Ajde_Cache')) {
					// 	Ajde_Cache::getInstance()->addFile($path);
					// }
					if ($debug) {
						echo "<span style='color:green;'>FOUND</span> $path <br/>";
						echo "<span style='font-size:smaller;color:gray;'>stats : (missed/lookups) : ".self::$missed."/".self::$queries." : ".(int) (self::$missed / self::$queries * 100)."% missed</span><br/>";
					}
					require_once $path;
					return;
				} else {
					if ($debug) {
						echo "<span style='color:red;'>CONTINUE</span> $path <br/>";
						self::$missed++;
					}
				}
			}
		}

		/*
		 * Throwing exceptions is only possible as of PHP 5.3.0
		 * See: http://php.net/manual/en/language.oop5.autoload.php
		 */
		if (version_compare(PHP_VERSION, '5.3.0') >= 0 && self::exists('Ajde_Core_Autoloader_Exception'))
		{
			// TODO: Custom Exceptions are still causing problems
			// throw new Ajde_Core_Autoloader_Exception("Unable to load $className", 90005);
			throw new Exception("Unable to load $className", 90005);
		}
	}

	public static function exists($className)
	{
		try
		{
			// Pre PHP 5.3.0
			if (!class_exists($className)) {
				return false;
			}
		}
		catch (Exception $exception)
		{
			// 90005: Unable to load CLASSNAME
			if ($exception->getCode() === 90005)
			{
				return false;
			}
			else
			{
				throw $exception;
			}
		}
		return true;
	}
}