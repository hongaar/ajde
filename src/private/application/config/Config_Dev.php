<?php
require_once 'Config_Application.php';

class Config_Dev extends Config_Application {
	
	// Performance
	public $compressResources	= false;
	public $debug 				= true;
	public $useCache			= false;

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