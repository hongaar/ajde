<?php
/**
 * Ajde - friendly CMS
 *
 * @author Nabble, Joram van den Boezem
 * @see https://github.com/nabble/ajde
 */

// --------------------
// Define Ajde version, needed for database migration
// --------------------
define('AJDE_VERSION', 'v0.4');

// --------------------
// Check PHP version
// --------------------
if (version_compare(PHP_VERSION, '5.3.0') < 0) {
    die('<p>Ajde requires PHP/5.3.0 or higher.</p>');
}

// --------------------
// Hide all errors
// --------------------
error_reporting(0);

// --------------------
// PHP settings (also defined in .htaccess)
// --------------------
// PHP compatibility settings
ini_set('short_open_tag', 0);
ini_set('magic_quotes_gpc', 0);
ini_set('register_globals', 0);

// Max upload size
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '100M');

// Hide PHP errors
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// --------------------
// Define paths
// --------------------
define('DS', DIRECTORY_SEPARATOR);
define('CONFIG_DIR', 'config' . DS);
define('LAYOUT_DIR', 'layout' . DS);
define('MODULE_DIR', 'modules' . DS);
define('TEMPLATE_DIR', 'template' . DS);

define('APP_DIR', 'app' . DS);
define('LANG_DIR', APP_DIR . 'lang' . DS);

define('CORE_DIR', 'core' . DS);
define('DEV_DIR', 'dev' . DS);
define('LIB_DIR', 'lib' . DS);
define('VENDOR_DIR', 'vendor' . DS);

define('VAR_DIR', 'var' . DS);
define('CACHE_DIR', VAR_DIR . 'cache' . DS);
define('LOG_DIR', VAR_DIR . 'log' . DS);
define('TMP_DIR', VAR_DIR . 'tmp' . DS);

define('PUBLIC_DIR', 'public' . DS);
define('ASSETS_DIR', PUBLIC_DIR . 'assets' . DS);
define('MEDIA_DIR', ASSETS_DIR . 'media' . DS);
define('UPLOAD_DIR', MEDIA_DIR . 'upload' . DS);
define('AVATAR_DIR', UPLOAD_DIR . 'avatar' . DS);

// --------------------
// Require composer autoloader
// --------------------
require_once VENDOR_DIR . 'autoload.php';

/*********************
 * GLOBAL FUNCTIONS
 *********************/

// --------------------
// The only thing missing in PHP < 5.3
// In PHP 5.3 you can use: return $test ?: false;
// This translates in Ajde to return issetor($test);
// --------------------
function issetor(&$what, $else = null) {
    // @see http://fabien.potencier.org/article/48/the-php-ternary-operator-fast-or-not
    if (isset($what)) {
        return $what;
    } else {
        return $else;
    }
}

// --------------------
// Global helper functions
// --------------------
function dump($var, $expand = true) {
    Ajde_Dump::dump($var, $expand);
}

function dd($var) {
    while (ob_get_level()) {
        ob_end_clean();
    }
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

// --------------------
// Speed up autoloading
// --------------------
require_once(LIB_DIR . "Ajde/Ajde.php");

// --------------------
// Install the db?
// --------------------
if (isset($_GET['install']) && $_GET['install'] == '1' && is_file('install.php')) {
    require('install.php');
    exit;
}

// --------------------
// Run the main application
// --------------------
$app = Ajde::create();

try {
    $app->run();
} catch (Ajde_Core_Exception_Deprecated $e) {
    // Throw $e to die on deprecated functions / methods (only in debug mode)
    throw $e;
}
