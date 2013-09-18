<?php
require_once CORE_DIR . CONFIG_DIR . 'Config_Cms.php';

class Config_Application extends Config_Cms
{
	// Site parameters
	public $ident				= 'project';
	public $sitename 			= 'Project name';
	public $description			= 'Project description';
	public $author				= 'Author name';
	public $email				= 'info@example.nl';
	public $version 			= array(
									'number' => '1',
									'name' => 'version description'
									);


	//public $homepageRoute;
	//public $defaultRouteParts;
	//public $aliases;
	//public $routes;

	//public $titleFormat;
	public $lang 				= 'en_GB';
	//public $langAutodetect;
	//public $langAdapter;
	//public $timezone;
	//public $layout;
	//public $responseCodeRoute;

	//public $autoEscapeString;
	//public $autoCleanHtml;
	//public $requirePostToken;
	//public $postWhitelistRoutes;
	public $secret				= 'randomstring';
	//public $cookieDomain;
	//public $cookieSecure;
	//public $cookieHttponly;

	//public $sessionLifetime;
	//public $sessionSavepath;

	//public $compressResources;
	//public $debug;
	//public $useCache;
	//public $documentProcessors;

	//public $dbAdapter;
	public $dbDsn				= array(
									'host' 		=> 'localhost',
									'dbname'	=> 'ajde_cms'
									);
	public $dbUser 				= 'ajde_user';
	public $dbPassword 			= 'ajde_pass';
	//public $textEditor;

	//public $registerNamespaces;
	//public $overrideClass;

	//public $transactionProviders;
	//public $currency;
	//public $currencyCode;
	//public $defaultVAT;

	//public $bootstrap;

    public $apiKeys = array(
        'google' => '',
        'soundcloud' => ''
    );

	function __construct() {
		parent::__construct();
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$this->sessionSavepath = '~/private/var/tmp'; // '~' gets replaced with local_root
		}
	}

	public function getParentClass()
	{
		return strtolower(str_replace('Config_', '', get_parent_class('Config_Application')));
	}

}