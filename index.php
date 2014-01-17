<?php

/*********************
 * AJDE OPEN WEB FRAMEWORK
 * https://github.com/hongaar/ajde
 *********************/

//	--------------------
//	We're running index.php
//	--------------------
	define('AJDE', true);
	
//	--------------------
//	Which version of Ajde are we running?
//	--------------------	
	define('AJDE_VERSION', 'v0.2.7');

/*********************
 * ERROR REPORTING
 *********************/

//	--------------------
//	Check PHP version
//	--------------------
	if (version_compare(PHP_VERSION, '5.2.3') < 0) {
		die('<h3>Ajde requires PHP/5.2.3 or higher.<br>You are currently running PHP/'.phpversion().'.</h3><p>You should contact your host to see if they can upgrade your version of PHP.</p>');
	}

//	--------------------
//	Show errors before errorhandler is initialized in bootstrapping
//	--------------------
	error_reporting(E_ALL);

//	--------------------
//	Redefine .htaccess settings, in case Ajde is running in CGI mode
//	--------------------
	// PHP compatibility settings
	ini_set('short_open_tag', 0);
	ini_set('magic_quotes_gpc', 0);
	ini_set('register_globals', 0);

	// Max upload size
	ini_set('upload_max_filesize', '15M');
	ini_set('post_max_size', '15M');

	// Force PHP errors
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);

	// Uncomment to hide uncatchable fatal errors
	//ini_set('display_errors', 0);

//	--------------------
//	Try to catch fatal errors
//	--------------------
	function shutdown()
	{
		if (($error = error_get_last()) && in_array($error['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR))) {
			$exception = new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
			if (Config::get('debug') === true) {
				echo Ajde_Exception_Handler::trace($exception);
			} else {
				// Use native PHP error log function, as Ajde_Exception_Log does not work
				error_log($error['message'] . ', ' . $error['type'] . ', ' . $error['file'] . ', ' . $error['line']);
				Ajde_Http_Response::dieOnCode(Ajde_Http_Response::RESPONSE_TYPE_SERVERERROR);
			}
		}
	}
	register_shutdown_function('shutdown');

/*********************
 * AUTOLOADER
 *********************/

//	--------------------
//	Define paths
//	--------------------
	define('PRIVATE_DIR',		'private' . DIRECTORY_SEPARATOR);
	define('PUBLIC_DIR',		'public' . DIRECTORY_SEPARATOR);
	define('CONFIG_DIR',		'config' . DIRECTORY_SEPARATOR);
	define('LAYOUT_DIR',		'layout' . DIRECTORY_SEPARATOR);
	define('MODULE_DIR',		'modules' . DIRECTORY_SEPARATOR);
	
	define('TEMPLATE_DIR',		'template' . DIRECTORY_SEPARATOR);
	
	define('LIB_DIR',			PRIVATE_DIR.'lib' . DIRECTORY_SEPARATOR);
	define('VAR_DIR',			PRIVATE_DIR.'var' . DIRECTORY_SEPARATOR);
	define('DEV_DIR',			PRIVATE_DIR.'dev' . DIRECTORY_SEPARATOR);
	
	define('MEDIA_DIR',			PUBLIC_DIR.'media' . DIRECTORY_SEPARATOR);
	
	define('UPLOAD_DIR',		MEDIA_DIR.'upload' . DIRECTORY_SEPARATOR);
    define('AVATAR_DIR',        UPLOAD_DIR.'avatar' . DIRECTORY_SEPARATOR);
	
	define('CACHE_DIR',			VAR_DIR.'cache' . DIRECTORY_SEPARATOR);
	define('LOG_DIR',			VAR_DIR.'log' . DIRECTORY_SEPARATOR);
	define('TMP_DIR',			VAR_DIR.'tmp' . DIRECTORY_SEPARATOR);

	define('CORE_DIR',			PRIVATE_DIR.'ajde' . DIRECTORY_SEPARATOR);
	define('APP_DIR',			PRIVATE_DIR.'application' . DIRECTORY_SEPARATOR);	
	
	define('LANG_DIR',			APP_DIR.'lang' . DIRECTORY_SEPARATOR);
	
//	--------------------
//	Zend requires include path to be set to the LIB directory
//	--------------------
	set_include_path(get_include_path() . PATH_SEPARATOR . LIB_DIR);

//	--------------------
//	Configure the autoloader
//	--------------------
	require_once(LIB_DIR."Ajde/Core/Autoloader.php");
	Ajde_Core_Autoloader::register();

/*********************
 * GLOBAL FUNCTIONS
 *********************/

//	--------------------
//	The only thing missing in PHP < 5.3
//	In PHP 5.3 you can use: return $test ?: false;
//	This translates in Ajde to return issetor($test);
//	--------------------
	function issetor(&$what, $else = null)
	{
		// @see http://fabien.potencier.org/article/48/the-php-ternary-operator-fast-or-not
		if (isset($what)) {
			return $what;
		} else {
			return $else;
		}
	}

//	--------------------
//	Global helper functions
//	--------------------
	function dump($var, $expand = true) {
		Ajde_Dump::dump($var, $expand);
	}
	
	function dd($var) {
		while (ob_get_level()) { ob_end_clean(); } 
		die(var_dump($var));
	}
	
	/**
	 * Translates the string with Ajde_Lang::translate
	 * 
	 * @param string $ident
	 * @param string $module
	 * @return string
	 */
	function __($ident, $module = null) {
		return Ajde_Lang::getInstance()->translate($ident, $module);
	}
	
	/**
	 * Escapes the string with Ajde_Component_String::escape
	 * 
	 * @param string $var
	 * @return string
	 */
	function _e($var) {
		return Ajde_Component_String::escape($var);
	}
	
	/**
	 * Cleans the string with Ajde_Component_String::clean
	 * 
	 * @param string $var
	 * @return string
	 */
	function _c($var) {
		return Ajde_Component_String::clean($var);
	}


/*********************
 * LET'S RUN THINGS
 *********************/

//	--------------------
//	Speed up autoloading
//	--------------------
	require_once(LIB_DIR . "Ajde/Ajde.php");

//	--------------------
//	Install the db?
//	--------------------
	if (isset($_GET['install']) && $_GET['install'] == '1' && is_file('install.php')) {
		require('install.php');
		exit;
	}
	
//	--------------------
//	Run the main application
//	--------------------
	$app = Ajde::create();

	try {
		$app->run();
	} catch (Ajde_Core_Exception_Deprecated $e) {
		// Throw $e to die on deprecated functions / methods (only in debug mode)
		throw $e;
	}