<?php
require_once 'Config_Application.php';

class Config_Dev extends Config_Application
{
    // Performance
    public $compressResources = false;
    public $debug             = true;
    public $useCache          = false;

    public $logLevel = '8:Debug';

    // Mail
    // Uncomment to use Gmail SMPT server locally
    //    public $mailer				= 'smtp';
    //    public $mailerConfig		= array(
    //        'Host' => 'smtp.gmail.com',
    //        'Port' => '587',
    //        'SMTPAuth' => true,
    //        'SMTPSecure' => 'tls',
    //        'Username' => 'user@gmail.com',
    //        'Password' => 'password'
    //    );
    public $mailerDebug = true;

    function __construct()
    {
        parent::__construct();
        $this->documentProcessors['html'][] = 'Debugger';
    }

}
