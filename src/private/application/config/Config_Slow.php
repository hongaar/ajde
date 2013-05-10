<?php
require_once 'Config_Application.php';

class Config_Slow extends Config_Application {

	// Performance
	public $compressResources	= true;
	public $debug 				= true;
	public $useCache			= false;

	function __construct() {
		parent::__construct();
		$this->documentProcessors['css'] = array('Less');
		$this->documentProcessors['html'][] = 'Debugger';	
//		$this->documentProcessors['html'][] = 'Compressor';
	}
	
}