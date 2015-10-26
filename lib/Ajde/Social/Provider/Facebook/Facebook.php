<?php

require_once "Facebook.lib.php";

class Ajde_Social_Provider_Facebook extends Facebook
{
    private $_key;
    private $_secret;

    public function __construct($config = [])
    {
        $this->_key = Config::get('ssoFacebookKey');
        $this->_secret = Config::get('ssoFacebookSecret');

        $config = array_merge($config, [
            'appId' => $this->_key,
            'secret' => $this->_secret,
        ]);

        parent::__construct($config);
    }
}
