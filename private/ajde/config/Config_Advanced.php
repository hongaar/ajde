<?php

class Config_Advanced
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
	public $email				= null;
	public $version 			= array(
									'number' => null,
									'name' => null
									);

	// Routing
	public $homepageRoute		= 'home';
	public $defaultRouteParts	= array(
									'module' => 'main',
									'controller' => null,
									'action' => 'view',
									'format' => 'html'
									);
	public $aliases				= array(
									'home' => 'main.html'
									);
	public $routes				= array(
									);

	// Front-end
	public $titleFormat			= '%2$s - %1$s'; // %1$s is project title, %2$s is document title
	public $lang 				= 'en_GB';
	public $langAutodetect		= true;
	public $langAdapter			= 'ini';
	public $timezone			= 'Europe/Amsterdam'; // 'UTC' for Greenwich Mean Time
	public $layout 				= 'advanced';
	public $responseCodeRoute	= array(
									'401' => 'user/logon.html',
									'403' => 'main/code403.html',
									'404' => 'main/code404.html',
									'500' => 'main/code500.html'
								);
	public $browserSupport		= array(

								);

	// Security
	public $autoEscapeString	= true;
	public $autoCleanHtml		= true;
	public $requirePostToken	= true;
	public $postWhitelistRoutes	= array(
									'shop/transaction:callback',
									'shop/transaction:complete',
									'shop/transaction:refused'
									);
	public $secret				= null; // set in Config_Application
	public $cookieDomain		= false;
	public $cookieSecure		= false;
	public $cookieHttponly		= true;

	// Session
	public $sessionLifetime		= 60; // in minutes
	public $sessionSavepath		= false; // '~' gets replaced with local_root

	// Performance
	public $compressResources	= true;
	public $debug 				= false;
    public $logWriter           = array('db', 'file');
	public $useCache			= true;
	public $documentProcessors	= array(
									'css'	=> array(
											'Less',
// 											'Maximizer' // disable, it's not so efficient
									)
								  );

	// Database
	public $dbAdapter			= 'mysql';
	public $dbDsn				= array(
									'host' 		=> 'localhost',
									'dbname'	=> 'ajde_cms'
									);
	public $dbUser 				= 'ajde_user';
	public $dbPassword 			= 'ajde_pass';
	public $textEditor			= 'ckeditor'; // Use this text editor for CRUD operations (aloha|jwysiwyg|ckeditor)
	
	// Mailer
	public $mailer				= 'mail'; // One of: mail|smtp
	public $mailerSmtpHost		= false;
	public $mailerDebug			= false;

	// Custom libraries
	public $registerNamespaces	= array();
	public $overrideClass		= array();

    // User login
    public $ssoProviders        = array('google', 'facebook', 'twitter');

        // Twitter
        public $ssoTwitterKey       = false;
        public $ssoTwitterSecret    = false;

        // Facebook
        public $ssoFacebookKey      = false;
        public $ssoFacebookSecret   = false;

	// Shop
	public $transactionProviders= array('paypal_creditcard', 'paypal', 'mollie_ideal');
	public $currency			= '€';
	public $currencyCode		= 'EUR';
	public $defaultVAT			= 0.19;
	public $shopSandboxPayment	= true;

		// PayPal
		public $shopPaypalAccount			= 'info@example.com';

		// Wedeal
		public $shopWedealUsername			= 'user';
		public $shopWedealPassword			= 'pass';
		public $shopWedealCallbackUsername	= 'user';
		public $shopWedealCallbackPassword	= 'pass';

        // Mollie
        public $shopMollieLiveKey           = 'live_key';
        public $shopMollieTestKey           = 'test_key';

    // Which modules should we call on bootstrapping?
	public $bootstrap			= array(
									'Ajde_Exception_Handler',
									'Ajde_Session',
									'Ajde_Core_ExternalLibs',
									'Ajde_User_Autologon',
									'Ajde_Core_Autodebug',
									'Ajde_Shop_Cart_Merge',
									'Ajde_Cms'
									);

	function __construct()
	{
		// Root project on local filesystem
		$this->local_root = $_SERVER['DOCUMENT_ROOT'] . str_replace('/index.php', '', $_SERVER['PHP_SELF']);
		
		// URI fragments
		$this->site_protocol = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) ? 'https://' : 'http://';
		$this->site_domain = $_SERVER['SERVER_NAME'];
		$this->site_path = str_replace('index.php', '', $_SERVER['PHP_SELF']);
		
		// Assembled URI
		$this->site_root = $this->site_protocol . $this->site_domain . $this->site_path;
		
		// Assembled URI with language identifier
		$this->lang_root = $this->site_root;
		
		// Set default timezone now
		date_default_timezone_set($this->timezone);
	}

}