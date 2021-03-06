<?php

require_once 'Google/Client.php';

class Ajde_Social_Provider_Google extends Google_Client
{
    private $_key;
    private $_secret;

    public function __construct($config = null)
    {
        parent::__construct($config);

        $this->_key = config('services.google.key');
        $this->_secret = config('services.google.secret');

        $this->setClientId($this->_key);
        $this->setClientSecret($this->_secret);
    }

    public function getPlus()
    {
        require_once 'Google/Service/Plus.php';

        return new Google_Service_Plus($this);
    }
}
