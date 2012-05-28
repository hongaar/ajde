<?php
require_once 'Config_Simple.php';
require_once 'Config_Advanced.php';

class Config_Application extends Config_Advanced
{	
	// Site parameters
	public $ident				= 'creactor';
	public $sitename 			= 'Creactor';
	public $description			= 'Create beautiful websites';	
	public $author				= 'Nabble';
	public $version 			= array(
									'number' => '0.1',
									'name' => 'alpha'
									);
									
									
	// Routing
	public $homepageRoute		= 'creactor.html';
	public $defaultRouteParts	= array(
									'module' => 'creactor',
									'controller' => null,
									'action' => 'view',
									'format' => 'html'
									);       
	public $aliases				= array(									
									);											
	public $routes				= array(
									);
									
	// Front-end
	public $titleFormat			= '%2$s - %1$s'; // %1$s is project title, %2$s is document title
	public $lang 				= 'en_GB';
	public $langAutodetect		= true;
	public $langAdapter			= 'ini';
	public $timezone			= 'Europe/Amsterdam'; // 'UTC' for Greenwich Mean Time
	public $layout 				= 'creactor';
	public $responseCodeRoute	= array(
									'404' => 'creactor/code404.html',
									'401' => 'user/logon.html'
								  );
	
	//public $autoEscapeString;
	//public $autoCleanHtml;
	//public $requirePostToken;
	//public $postWhitelistRoutes;
	public $secret				= '2zhLqKYUaS7L0z5ZLZmWox4suKzxZBQm';
	//public $cookieDomain;
	//public $cookieSecure;
	//public $cookieHttponly;
	
	//public $cookieLifetime;
	//public $gcLifetime;
	
	//public $compressResources;
	//public $debug;
	//public $useCache;
	//public $documentProcessors;
	
	// Database
	public $dbAdapter			= 'mysql';
	public $dbDsn				= array(
									'host' 		=> 'localhost',
									'dbname'	=> 'creactor'
									);
	public $dbUser 				= 'creactor';
	public $dbPassword 			= 'cxJZbDfGX6RepVw3';	
	public $textEditor			= 'ckeditor'; // Use this text editor for CRUD operations (aloha|jwysiwyg|ckeditor) 
	
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