<?php

class Config_Simple
{
	/**
	 * Please do not edit this configuration file, this makes it easier
	 * to upgrade when defaults are changed or new values are introduced.
	 * Instead, use Config_Application to override default values. 
	 */
		
	// Site parameters, defined in Config_Application
	public $ident				= null;
	public $sitename 			= null;	
	public $description			= null;	
	public $author				= null;
	public $version 			= array(
									'number' => null,
									'name' => null
									);
									
	// Routing
	public $homepageRoute		= 'home.html';
	public $defaultRouteParts	= array(
									'module' => 'main',
									'controller' => null,
									'action' => 'view',
									'format' => 'html'
									);       
	public $aliases				= array(
									'home.html' => 'main.html'
									);											
	public $routes				= array(
									);
									
	// Front-end
	public $titleFormat			= '%2$s - %1$s'; // %1$s is project title, %2$s is document title
	public $lang 				= 'en_GB';
	public $langAutodetect		= false;
	public $langAdapter			= 'ini';
	public $timezone			= 'Europe/Amsterdam'; // 'UTC' for Greenwich Mean Time
	public $layout 				= 'simple';
	public $responseCodeRoute	= array();
	
	// Security
	public $autoEscapeString	= true;
	public $autoCleanHtml		= true;
	public $requirePostToken	= false;
	public $postWhitelistRoutes = array();
	public $secret				= 'randomstring';
	public $cookieDomain		= false;
	public $cookieSecure		= false;
	public $cookieHttponly		= true;
	
	// Session
	public $cookieLifetime		= 0; // in minutes, 0 = session
	public $gcLifetime			= 60; // PHP session garbage collection timeout in minutes
	
	// Performance
	public $compressResources	= true;
	public $debug 				= false;
	public $useCache			= true;
	public $documentProcessors	= array();
	
	// Database
	public $dbAdapter			= null;
	public $dbDsn				= array(
									'host' 		=> null,
									'dbname'	=> null
									);
	public $dbUser 				= null;
	public $dbPassword 			= null;
	public $textEditor			= 'none'; // Use this text editor for CRUD operations (aloha|jwysiwyg|ckeditor) 

	// Custom libraries
	public $registerNamespaces	= array();
	public $overrideClass		= array();

	// Shop
	public $transactionProviders= array();
	public $currency			= null;
	public $currencyCode		= null;
	public $defaultVAT			= null;
	
	// Which modules should we call on bootstrapping?
	public $bootstrap			= array(									
									'Ajde_Exception_Handler'
									);

	function __construct()
	{
		$this->local_root = $_SERVER['DOCUMENT_ROOT'] . str_replace('/index.php', '', $_SERVER['PHP_SELF']);
		$this->site_domain = $_SERVER['SERVER_NAME'];
		$this->site_path = str_replace('index.php', '', $_SERVER['PHP_SELF']);
		$this->site_root = $this->site_domain . $this->site_path;
		$this->lang_root = $this->site_root;
		date_default_timezone_set($this->timezone);
	}
	
}