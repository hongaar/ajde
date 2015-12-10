<?php
require_once 'Config_Application.php';

class Config_Live extends Config_Application
{
    // Performance
    public $compressResources = true;
    public $debug = false;
    public $useCache = true;

    public $logLevel = '4:Error';

    function __construct()
    {
        parent::__construct();
        $this->documentProcessors['html'] = [];
    }

}
