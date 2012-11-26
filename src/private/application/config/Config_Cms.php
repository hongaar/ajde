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
	//public $defaultRouteParts;
	//public $aliases;
	//public $routes;

	//public $titleFormat;
	//public $lang;
	//public $langAutodetect;
	//public $langAdapter;
	//public $timezone;
	public $layout						= 'cms';
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