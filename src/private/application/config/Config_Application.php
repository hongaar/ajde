<?php
require_once 'Config_Cms.php';

class Config_Application extends Config_Cms
{
	// Site parameters
	public $ident				= 'belay';
	public $sitename 			= 'Belay';
	public $description			= 'Project management';
	public $author				= 'Nabble';
	public $email				= 'joram@nabble.nl';
	public $version 			= array(
									'number' => '0.1',
									'name' => 'alpha'
									);


	public $homepageRoute		= 'belay';
	public $defaultRouteParts	= array(
									'module' => 'belay',
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
	public $layout				= 'belay';
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
									'dbname'	=> 'belay'
									);
	public $dbUser 				= 'belay';
	public $dbPassword 			= 'belay';
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