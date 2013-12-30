<?php
require_once 'Config_Advanced.php';

class Config_Cms extends Config_Advanced
{
	//public $ident;
	//public $sitename;
	//public $description;
	//public $author;
	//public $email;
	//public $version;

	//public $homepageRoute;
	public $defaultRouteParts	= array(
									'module' => 'node',
									'controller' => null,
									'action' => 'view',
									'format' => 'html',
									'nodetype' => null,
									'slug' => null,
									'id' => null									
								);
	public $aliases				= array(
									'home' => '-homepage/home.html'
						  		);	
	public $routes				= array(
									array('%^-([^/\.]+)/([^/\.]+)$%' => array('nodetype', 'slug')),
									array('%^-([^/\.]+)/([^/\.]+)\.(html)$%' => array('nodetype', 'slug', 'format')),
								);

	//public $titleFormat;
	//public $lang;
	//public $langAutodetect;
	//public $langAdapter;
	//public $timezone;
	public $layout				= 'cms';
	public $adminLayout			= 'admin';
	//public $responseCodeRoute;

	//public $autoEscapeString;
	//public $autoCleanHtml;
	//public $requirePostToken;
	//public $postWhitelistRoutes;
	//public $secret;
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
	//public $dbUser;
	//public $dbPassword;
	//public $textEditor;

	//public $registerNamespaces;
	//public $overrideClass;

    //public $ssoProviders;

	//public $transactionProviders;
	//public $currency;
	//public $currencyCode;
	//public $defaultVAT;
    //public $shopSandboxPayment;

	//public $bootstrap;

	public function getParentClass()
	{
		return strtolower(str_replace('Config_', '', get_parent_class('Config_Application')));
	}

}