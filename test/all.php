<?php	
require_once('simpletest/autorun.php');

// Set include path for addFile function
$testPath = $_SERVER['DOCUMENT_ROOT'] . '/test/';
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/';
set_include_path(get_include_path() . PATH_SEPARATOR . $testPath . PATH_SEPARATOR . $rootPath);

// Define paths
define('PRIVATE_DIR', 		'private/');
define('PUBLIC_DIR', 		'public/');
define('TEMPLATE_DIR', 		'template/');
define('APP_DIR', 			PRIVATE_DIR.'application/');
define('LIB_DIR', 			PRIVATE_DIR.'lib/');
define('VAR_DIR', 			PRIVATE_DIR.'var/');
define('CACHE_DIR', 		VAR_DIR.'cache/');
define('CONFIG_DIR', 		APP_DIR.'config/');
define('LAYOUT_DIR', 		APP_DIR.'layout/');
define('LOG_DIR', 			VAR_DIR.'log/');
define('MODULE_DIR', 		APP_DIR.'modules/');

// Configure the autoloader
require_once('../' . LIB_DIR . 'Ajde/Core/Autoloader.php');
$dirPrepend = $_SERVER['DOCUMENT_ROOT'] . '/';
Ajde_Core_Autoloader::register($dirPrepend);

class AllTests extends TestSuite {
	
    function __construct() {
    	$this->TestSuite('Ajde test suite');
        $this->addFile('testCore.php');
        $this->addFile('testXhtml.php');
		$this->addFile('testPhtml.php');
		$this->addFile('testZend.php');
    }
    
}