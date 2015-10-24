<?php

require_once 'twitteroauth.php';

class Ajde_Social_Provider_Twitter extends TwitterOAuth
{
    private $_key;
    private $_secret;

    function __construct($oauth_token = null, $oauth_token_secret = null) {
        $this->_key = Config::get('ssoTwitterKey');
        $this->_secret = Config::get('ssoTwitterSecret');

        parent::__construct($this->_key, $this->_secret, $oauth_token, $oauth_token_secret);
    }

}