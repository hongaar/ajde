<?php

class Ajde_User_Sso_Google extends Ajde_User_Sso
{
    private $_session;
    private $_provider;
    private $_plus;
    private $_credentials = false;
    private $_me;

    const SSO_SESSION_KEY = 'sso.google.credentials';

    public static $iconName = 'google-plus';
    public static $color    = 'db4a39';

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
        $google = new Ajde_Social_Provider_Google();
        $google->setRedirectUri(config("app.rootUrl") . 'user/sso:callback?provider=google');
        $google->setScopes("https://www.googleapis.com/auth/plus.login");
        $google->setScopes('https://www.googleapis.com/auth/plus.profile.emails.read');
        $this->_plus     = $google->getPlus();
        $this->_provider = $google;
        if ($this->_credentials) {
            $this->_provider->setAccessToken($this->_credentials['oauth_token']);
        }
    }

    public static function fromModel(SsoModel $sso)
    {
        $instance = new self(unserialize($sso->getData()));

        return $instance;
    }

    /**
     * @return Ajde_Social_Provider_Google
     */
    public function getProvider()
    {
        return $this->_provider;
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
                $this->_me = $this->_plus->people->get('me');
            }

            return $this->_me;
        }
        throw new Ajde_Exception('Provider Google has no authenticated user');
    }

    public function getAuthenticationURL($returnto = '')
    {
        $connection = $this->getProvider();

        // set returnto in session, google is very strict with callback urls
        $returntoSession = new Ajde_Session('returnto');
        $returntoSession->set("url", $returnto);

        return $connection->createAuthUrl();
    }

    public function isAuthenticated()
    {
        if (!Ajde::app()->getRequest()->getParam('code', false)) {
            return false;
        }

        $connection = $this->getProvider();
        $code       = Ajde::app()->getRequest()->getRaw('code');

        $connection->authenticate($code);
        $this->setCredentials([
            'oauth_token' => $connection->getAccessToken()
        ]);

        return true;
    }

    public function getUsernameSuggestion()
    {
        if ($this->hasCredentials()) {
            $me   = $this->getMe();
            $name = $me->__get('name');

            return $name['givenName'];
        } else {
            return false;
        }
    }

    public function getEmailSuggestion()
    {
        if ($this->hasCredentials()) {
            $me     = $this->getMe();
            $emails = $me->__get('emails');

            return $emails[0]['value'];
        } else {
            return false;
        }
    }

    public function getNameSuggestion()
    {
        if ($this->hasCredentials()) {
            return $this->getMe()->getDisplayName();
        } else {
            return false;
        }
    }

    public function getAvatarSuggestion()
    {
        $size = 150;
        if ($this->hasCredentials()) {
            $image = $this->getMe()->getImage();
            if ($image instanceof Google_Service_Plus_PersonImage) {
                $url = $image->getUrl();

                return str_replace('?sz=50', '?sz=' . $size, $url);
            }
        }
    }

    public function getProfileSuggestion()
    {
        return '';
    }

    public function getUidHash()
    {
        if ($this->hasCredentials()) {
            return md5(md5($this->getMe()->getId()));
        } else {
            return false;
        }
    }

    public function getData()
    {
        return $this->hasCredentials() ? $this->getCredentials() : false;
    }
}
