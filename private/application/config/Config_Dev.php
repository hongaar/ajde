<?php
require_once 'Config_Application.php';

class Config_Dev extends Config_Application {

	// Performance
	public $compressResources	= false;
	public $debug 				= true;
	public $useCache			= false;

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
    public $mailerDebug			= true;

	function __construct() {
		parent::__construct();
		$this->documentProcessors['html'][] = 'Debugger';
		// Disable Beautifier processor by default
		// as Tidy class is not included in quite
		// some PHP builds
		// @see https://code.google.com/p/ajde/wiki/Exception90023
		//$this->documentProcessors['html'][] = 'Beautifier';
	}
	
}