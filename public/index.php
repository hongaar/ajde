<?php
/**
 * Ajde - friendly CMS.
 *
 * @author Joram van den Boezem, Nabble
 *
 * @see    https://github.com/nabble/ajde
 */

// --------------------
// Check PHP version
// --------------------
if (version_compare(PHP_VERSION, '5.5.0') < 0) {
    die('Ajde requires PHP/5.5.0 or higher.');
}

// --------------------
// Hide all errors
// --------------------
error_reporting(E_ALL);

// --------------------
// PHP settings
// --------------------
// PHP compatibility settings
ini_set('short_open_tag', 0);
ini_set('magic_quotes_gpc', 0);
ini_set('register_globals', 0);

// Max upload size
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');

// Hide PHP errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);

// --------------------
// Define paths
// --------------------
define('DS', DIRECTORY_SEPARATOR);
define('UP', '..'.DS);

// Define root
define('LOCAL_ROOT', realpath(__DIR__.DS.UP).DS);

// Main directories
define('APP_DIR', 'app'.DS);
define('CORE_DIR', 'core'.DS);
define('DEV_DIR', 'dev'.DS);
define('LIB_DIR', 'lib'.DS);
define('VENDOR_DIR', 'vendor'.DS);
define('VAR_DIR', 'var'.DS);
define('PUBLIC_DIR', 'public'.DS);

// These paths could exist in both app or core dir
define('CONFIG_DIR', 'config'.DS);
define('LAYOUT_DIR', 'layout'.DS);
define('MODULE_DIR', 'modules'.DS);
define('TEMPLATE_DIR', 'template'.DS);

// These paths only exist in app dir
define('LANG_DIR', APP_DIR.'lang'.DS);

// Var subdirectories
define('CACHE_DIR', VAR_DIR.'cache'.DS);
define('LOG_DIR', VAR_DIR.'log'.DS);
define('TMP_DIR', VAR_DIR.'tmp'.DS);

// Asset directories and URIs
define('ASSETS_URI', 'assets'.DS);
define('MEDIA_URI', ASSETS_URI.'media'.DS);
define('UPLOAD_URI', MEDIA_URI.'upload'.DS);
define('AVATAR_URI', UPLOAD_URI.'avatar'.DS);

define('ASSETS_DIR', PUBLIC_DIR.ASSETS_URI);
define('MEDIA_DIR', PUBLIC_DIR.MEDIA_URI);
define('UPLOAD_DIR', PUBLIC_DIR.UPLOAD_URI);
define('AVATAR_DIR', PUBLIC_DIR.AVATAR_URI);

// --------------------
// Require composer autoloader
// --------------------
require_once LOCAL_ROOT.VENDOR_DIR.'autoload.php';

// --------------------
// Run the main application
// --------------------
$app = Ajde::create();
$app->run();
