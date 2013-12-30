<?php

class Ajde_User_SSO_Twitter implements Ajde_User_SSO
{
    private $_key;
    private $_secret;

    public function __construct()
    {
        $this->_key = Config::get('ssoTwitterKey');
        $this->_secret = Config::get('ssoTwitterSecret');
    }

    public function getAuthenticationURL($returnto = '')
    {
        /* Build TwitterOAuth object with client credentials. */
        $connection = new Ajde_Social_Provider_Twitter($this->_key, $this->_secret);

        /* Get temporary credentials. */
        $callbackUrl = Config::get('site_root') . 'user/sso:callback?provider=twitter&returnto=' . $returnto;
        $request_token = $connection->getRequestToken($callbackUrl);

        /* Save temporary credentials to session. */
        $session = new Ajde_Session('sso_twitter');
        $session->set('oauth_token', $token = $request_token['oauth_token']);
        $session->set('oauth_token_secret', $request_token['oauth_token_secret']);

        /* If last connection failed don't display authorization link. */
        switch ($connection->http_code) {
            case 200:
                /* Build authorize URL and redirect user to Twitter. */
                return $connection->getAuthorizeURL($token);
            default:
                /* Show notification if something went wrong. */
                throw new Ajde_Exception('Could not connect to Twitter (' . $connection->http_info . '). Refresh the page or try again later.');
        }
    }

    public function getAccessToken()
    {
        $session = new Ajde_Session('sso_twitter');
        $oauth_token = $session->get('oauth_token');
        $oauth_token_secret = $session->get('oauth_token_secret');

        /* Get intermediate object */
        $connection = new Ajde_Social_Provider_Twitter($this->_key, $this->_secret, $oauth_token, $oauth_token_secret);

        /* Get access token. */
        $verifier = Ajde::app()->getRequest()->getParam('oauth_verifier');
        $token_credentials = $connection->getAccessToken($verifier);

        $session->destroy('oauth_token');
        $session->destroy('oauth_token_secret');
        $session->set('credentials', $token_credentials);

        return $token_credentials;
    }

    public function getUser()
    {
        $session = new Ajde_Session('sso_twitter');
        $token_credentials = $session->get('credentials');

        $token = $token_credentials['oauth_token'];
        $model = new SsoModel();
        if ($model->loadByField('token', md5($token))) {
            $model->loadParent('user');
            return $model->getUser();
        } else {
            return false;
        }
    }

    public function getUsernameSuggestion()
    {
        $session = new Ajde_Session('sso_twitter');
        $token_credentials = $session->get('credentials');

        return $token_credentials['screen_name'];
    }

    public function getToken()
    {
        $session = new Ajde_Session('sso_twitter');
        $token_credentials = $session->get('credentials');

        return $token_credentials['oauth_token'];
    }

    public function getData()
    {
        $session = new Ajde_Session('sso_twitter');
        return $session->get('credentials');
    }
}