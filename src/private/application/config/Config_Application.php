<?php
require_once 'Config_Cms.php';

class Config_Application extends Config_Cms
{
	// Site parameters
	public $ident				= 'project';
	public $sitename 			= 'Project name';
	public $description			= 'Project description';
	public $author				= 'Author name';
	public $email				= 'info@example.com';
	public $version 			= array(
									'number' => '0.1',
									'name' => 'alpha'
									);


	public $homepageRoute		= 'main.html';
	public $defaultRouteParts	= array(
									'module' => 'node',
									'controller' => null,
									'action' => 'view',
									'format' => 'html',
									'nodetype' => null,
									'slug' => null,
									'id' => null									
									);
	//public $aliases;
	public $routes				= array(
									array('%^-([^/\.]+)/([^/\.]+)$%' => array('nodetype', 'slug')),
									array('%^-([^/\.]+)/([^/\.]+)\.(html)$%' => array('nodetype', 'slug', 'format')),
								);

	//public $titleFormat;
	//public $lang;
	//public $langAutodetect;
	//public $langAdapter;
	//public $timezone;
	public $layout				= 'advanced';
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