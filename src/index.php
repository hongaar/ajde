<?php

/**
 * AJDE OPEN WEB FRAMEWORK
 * http://code.google.com/p/ajde/
 */

define('AJDE', true);

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
	define('PRIVATE_DIR', 		'private/');
	define('PUBLIC_DIR', 		'public/');
	define('TEMPLATE_DIR', 		'template/');
	define('APP_DIR', 			PRIVATE_DIR.'application/');
	define('LIB_DIR', 			PRIVATE_DIR.'lib/');
	define('VAR_DIR', 			PRIVATE_DIR.'var/');
	define('CONFIG_DIR', 		APP_DIR.'config/');
	define('LAYOUT_DIR', 		APP_DIR.'layout/');
	define('MODULE_DIR', 		APP_DIR.'modules/');
	define('LANG_DIR', 			APP_DIR.'lang/');
	define('CACHE_DIR', 		VAR_DIR.'cache/');
	define('LOG_DIR', 			VAR_DIR.'log/');

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
	function dump($var, $collapse = false) {
		Ajde_Dump::dump($var, $collapse);
	}

	function __($ident, $module = null) {
		return Ajde_Lang::getInstance()->translate($ident, $module);
	}
	
	function _e($var) {
		return Ajde_Component_String::escape($var);
	}
	
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
//	Run the main application
//	--------------------
	$app = Ajde::create();

	try {
		$app->run();
	} catch (Ajde_Core_Exception_Deprecated $e) {
		// Throw $e to die on deprecated functions / methods (only in debug mode)
		throw $e;
	}