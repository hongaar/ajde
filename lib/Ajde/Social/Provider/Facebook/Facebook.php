<?php

require_once 'Facebook.lib.php';

class Ajde_Social_Provider_Facebook extends Facebook
{
    private $_key;
    private $_secret;

    public function __construct($config = [])
    {
        $this->_key = config('services.facebook.key');
        $this->_secret = config('services.facebook.secret');

        $config = array_merge($config, [
            'appId'  => $this->_key,
            'secret' => $this->_secret,
        ]);

        parent::__construct($config);
    }
}
