<?php

require_once 'twitteroauth.php';

class Ajde_Social_Provider_Twitter extends TwitterOAuth
{
    private $_key;
    private $_secret;

    public function __construct($oauth_token = null, $oauth_token_secret = null)
    {
        $this->_key = config('services.twitter.key');
        $this->_secret = config('services.twitter.secret');

        parent::__construct($this->_key, $this->_secret, $oauth_token, $oauth_token_secret);
    }
}
