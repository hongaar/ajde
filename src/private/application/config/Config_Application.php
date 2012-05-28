<?php
require_once 'Config_Simple.php';
require_once 'Config_Advanced.php';

class Config_Application extends Config_Advanced
{	
	// Site parameters
	public $ident				= 'project';
	public $sitename 			= 'Project name';
	public $description			= 'Project description';	
	public $author				= 'Author name';
	public $version 			= array(
									'number' => '0.1',
									'name' => 'alpha'
									);
									
									
	//public $homepageRoute;
	//public $defaultRouteParts;
	//public $aliases;
	//public $routes;
	
	//public $titleFormat;
	//public $lang;
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
	
	//public $cookieLifetime;
	//public $gcLifetime;
	
	//public $compressResources;
	//public $debug;
	//public $useCache;
	//public $documentProcessors;
	
	//public $dbAdapter;
	//public $dbDsn;	
	//public $dbUser;
	//public $dbPassword;	
	//public $textEditor;
	
	//public $registerNamespaces;
	//public $overrideClass;
	
	//public $transactionProviders;
	//public $currency;
	//public $currencyCode;
	//public $defaultVAT;
	
	//public $bootstrap;
	
	public function getParentClass()
	{
		return strtolower(str_replace('Config_', '', get_parent_class('Config_Application')));
	}
	
}