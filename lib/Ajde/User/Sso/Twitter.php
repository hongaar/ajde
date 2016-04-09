<?php

class Ajde_User_Sso_Twitter extends Ajde_User_Sso
{
    private $_session;
    private $_credentials = false;
    private $_me;

    const SSO_SESSION_KEY = 'sso.twitter.credentials';

    public static $iconName = 'twitter';
    public static $color    = '00acee';

    public function __construct($credentials = false)
    {
        $this->_session = new Ajde_Session('user');
        if ($credentials) {
            $this->setCredentials($credentials);
        } else {
            if ($this->_session->has(self::SSO_SESSION_KEY)) {
                $this->_credentials = $this->_session->get(self::SSO_SESSION_KEY);
            }
        }
    }

    public static function fromModel(SsoModel $sso)
    {
        $instance = new self(unserialize($sso->getData()));

        return $instance;
    }

    /**
     * @return Ajde_Social_Provider_Twitter
     */
    public function getProvider()
    {
        if ($this->_credentials) {
            return new Ajde_Social_Provider_Twitter($this->_credentials['oauth_token'],
                $this->_credentials['oauth_token_secret']);
        } else {
            return new Ajde_Social_Provider_Twitter();
        }
    }

    public function setCredentials($credentials)
    {
        $this->_session->set(self::SSO_SESSION_KEY, $credentials);
        $this->_credentials = $credentials;
    }

    public function getCredentials()
    {
        return $this->_credentials;
    }

    public function hasCredentials()
    {
        return isset($this->_credentials);
    }

    public function destroySession()
    {
        $this->_session->destroy(self::SSO_SESSION_KEY);
    }

    public static function getIconName()
    {
        return self::$iconName;
    }

    public static function getColor()
    {
        return self::$color;
    }

    public function getMe()
    {
        if ($this->hasCredentials()) {
            if (!isset($this->_me)) {
                $provider    = $this->getProvider();
                $credentials = $this->getCredentials();
                $this->_me   = $provider->get('users/show', [
                    'screen_name' => $credentials['screen_name']
                ]);
            }

            return $this->_me;
        }
        throw new Ajde_Exception('Provider Facebook has no authenticated user');
    }

    public function getAuthenticationURL($returnto = '')
    {
        $connection = $this->getProvider();

        /* Get temporary credentials. */
        $callbackUrl   = config("app.rootUrl") . 'user/sso:callback?provider=twitter&returnto=' . $returnto;
        $request_token = $connection->getRequestToken($callbackUrl);
        $this->setCredentials($request_token);

        /* If last connection failed don't display authorization link. */
        switch ($connection->http_code) {
            case 200:
                /* Build authorize URL and redirect user to Twitter. */
                return $connection->getAuthorizeURL($request_token);
            default:
                /* Show notification if something went wrong. */
                throw new Ajde_Exception('Could not connect to Twitter (' . $connection->http_info . '). Refresh the page or try again later.');
        }
    }

    public function isAuthenticated()
    {
        if (Ajde::app()->getRequest()->getParam('denied', false)) {
            return false;
        }

        $verifier   = Ajde::app()->getRequest()->getParam('oauth_verifier');
        $connection = $this->getProvider();
        $this->setCredentials($connection->getAccessToken($verifier));

        return true;
    }

    public function getUsernameSuggestion()
    {
        if ($this->hasCredentials()) {
            $credentials = $this->getCredentials();

            return $credentials['screen_name'];
        } else {
            return false;
        }
    }

    public function getEmailSuggestion()
    {
        return false;
    }

    public function getNameSuggestion()
    {
        return false;
    }

    public function getProfileSuggestion()
    {
        return '';
    }

    public function getAvatarSuggestion()
    {
        if ($this->hasCredentials()) {
            $me    = $this->getMe();
            $image = $me->profile_image_url;
            $image = str_replace('_normal.', '.', $image);

            return $image;
        } else {
            return false;
        }
        //        return false;
    }

    public function getUidHash()
    {
        if ($this->hasCredentials()) {
            $credentials = $this->getCredentials();

            return md5(md5($credentials['user_id']));
        } else {
            return false;
        }
    }

    public function getData()
    {
        return $this->hasCredentials() ? $this->getCredentials() : false;
    }
}
