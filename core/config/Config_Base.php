<?php

class Config_Base
{

    /**
     * Please do not edit this configuration file, this makes it easier
     * to upgrade when defaults are changed or new values are introduced.
     * Instead, use Config_Application to override default values.
     */

    // Site parameters, defined in Config_Application
    public $ident       = null;
    public $sitename    = null;
    public $description = null;
    public $author      = null;
    public $email       = null;
    public $version     = [
        'number' => null,
        'name' => null
    ];

    // Routing
    public $homepageRoute     = 'home';
    public $defaultRouteParts = [
        'module' => 'node',
        'controller' => null,
        'action' => 'view',
        'format' => 'html',
        'slug' => null,
        'id' => null
    ];
    public $aliases           = [];
    public $routes            = [];

    // Front-end
    public $titleFormat       = '%2$s - %1$s'; // %1$s is project title, %2$s is document title
    public $multiLang         = true;
    public $lang              = 'en_GB';
    public $langAutodetect    = true;
    public $langAdapter       = 'ini';
    public $timezone          = 'Europe/Amsterdam'; // 'UTC' for Greenwich Mean Time
    public $layout            = 'base';
    public $adminLayout       = 'admin';
    public $responseCodeRoute = [
        '401' => 'user/logon.html',
        '403' => 'main/code403.html',
        '404' => 'main/code404.html',
        '500' => 'main/code500.html'
    ];
    public $browserSupport    = [];

    // Security
    public $autoEscapeString    = true;
    public $autoCleanHtml       = true;
    public $requirePostToken    = true;
    public $postWhitelistRoutes = [
        'shop/transaction:callback',
        'shop/transaction:complete',
        'shop/transaction:refused'
    ];
    public $secret              = null; // set in Config_Application
    public $cookieDomain        = false;
    public $cookieSecure        = false;
    public $cookieHttponly      = true;

    // Session
    public $sessionLifetime = 60; // in minutes
    public $sessionSavepath = false; // '~' gets replaced with local_root

    // Performance
    public $compressResources  = true;
    public $debug              = false;
    public $logWriter          = ['db', 'file'];
    public $logLevel           = '5:Warning';
    public $useCache           = true;
    public $documentProcessors = [
        'html' => [],
        'css' => [
            'Less',
        ]
    ];

    // Database
    public $dbAdapter  = 'mysql';
    public $dbDsn      = [
        'host' => 'localhost',
        'dbname' => 'ajde'
    ];
    public $dbUser     = 'ajde_user';
    public $dbPassword = 'ajde_pass';
    public $textEditor = 'ckeditor'; // Use this text editor for CRUD operations (aloha|jwysiwyg|ckeditor)

    // Mailer
    public $mailer       = 'mail'; // One of: mail|smtp
    public $mailerConfig = [];
    public $mailerDebug  = false;

    // Custom libraries
    public $registerNamespaces = [];
    public $overrideClass      = [];

    // User login
    public $ssoProviders = ['google', 'facebook', 'twitter'];

    // Twitter
    public $ssoTwitterKey    = false;
    public $ssoTwitterSecret = false;

    // Facebook
    public $ssoFacebookKey    = false;
    public $ssoFacebookSecret = false;

    // Google
    public $ssoGoogleKey    = false;
    public $ssoGoogleSecret = false;

    // Shop
    public $shopOfferLogin       = true;
    public $transactionProviders = [
        'paypal_creditcard',
        'paypal',
        'mollie_ideal',
        'iban'
    ];
    public $currency             = '€';
    public $currencyCode         = 'EUR';
    public $defaultVAT           = 0.21;
    public $shopSandboxPayment   = true;

    // PayPal
    public $shopPaypalAccount = 'info@example.com';

    // Wedeal
    public $shopWedealUsername         = 'user';
    public $shopWedealPassword         = 'pass';
    public $shopWedealCallbackUsername = 'user';
    public $shopWedealCallbackPassword = 'pass';

    // Mollie
    public $shopMollieLiveKey = 'live_key';
    public $shopMollieTestKey = 'test_key';

    // PDF generator
    public $pdfMethod     = 'snappy';
    public $pdfWeb2PdfApi = '';

    // Which modules should we call on bootstrapping?
    public $bootstrap = [
        'Ajde_Exception_Handler',
        'Ajde_Session',
        'Ajde_Core_ExternalLibs',
        'Ajde_User_Autologon',
        'Ajde_Core_Autodebug',
        'Ajde_Shop_Cart_Merge',
        'Ajde_Cms',
        'Bootstrap'
    ];

    function __construct()
    {
        // Root project on local filesystem
        $this->local_root = $_SERVER['DOCUMENT_ROOT'] . str_replace('/index.php', '', $_SERVER['PHP_SELF']);

        // URI fragments
        $this->site_protocol = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) ? 'https://' : 'http://';
        $this->site_domain   = $_SERVER['SERVER_NAME'];
        $this->site_path     = str_replace(PUBLIC_URI . 'index.php', '', $_SERVER['PHP_SELF']);

        // Assembled URI
        $this->site_root = $this->site_protocol . $this->site_domain . $this->site_path;

        // Assembled URI with language identifier
        $this->lang_root = $this->site_root;

        // Set default timezone now
        date_default_timezone_set($this->timezone);
    }

}
