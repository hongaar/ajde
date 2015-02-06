<?php

class Ajde_User_Sso_Facebook extends Ajde_User_Sso
{
    private $_provider;
    private $_credentials = false;
    private $_me;

    public static $iconName = 'facebook';
    public static $color = '3b5998';

    public function __construct($credentials = false)
    {
        if ($credentials) {
            $this->setCredentials($credentials);
        }
        $facebook = new Ajde_Social_Provider_Facebook();
        if ($facebook->getUser()) {
            $this->setCredentials(array(
                'oauth_token' => $facebook->getAccessToken()
            ));
        }
        $this->_provider = $facebook;
    }

    public static function fromModel(SsoModel $sso)
    {
        $instance = new self(unserialize($sso->getData()));
        return $instance;
    }

    /**
     * @return Ajde_Social_Provider_Facebook
     */
    public function getProvider()
    {
       return $this->_provider;
    }

    public function setCredentials($credentials)
    {
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
        // nothing here..
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
                $this->_me = $this->getProvider()->api('/me');
            }
            return $this->_me;
        }
        throw new Ajde_Exception('Provider Facebook has no authenticated user');
    }

    public function getAuthenticationURL($returnto = '')
    {
        $connection = $this->getProvider();

        /* Set callback URL */
        $callbackUrl = Config::get('site_root') . 'user/sso:callback?provider=facebook&returnto=' . $returnto;

        return $connection->getLoginUrl(array(
            'redirect_uri' => $callbackUrl,
            'scope' => 'email'
        ));
    }

    public function isAuthenticated()
    {
        if (Ajde::app()->getRequest()->getParam('error_code', false)) {
            return false;
        }
        return true;
    }

    public function getUsernameSuggestion()
    {
        if ($this->hasCredentials()) {
            $me = $this->getMe();
            if (isset($me['username'])) {
                return $me['username'];
            } else if (isset($me['name'])) {
                $name = explode(' ', $me['name']);
                return $name[0];
            }
        }
        return false;
    }

    public function getProfileSuggestion()
    {
        if ($this->hasCredentials()) {
            $me = $this->getMe();
            return 'https://www.facebook.com/' . $me['id'];
        } else {
            return false;
        }
    }

    public function getEmailSuggestion()
    {
        if ($this->hasCredentials()) {
            $me = $this->getMe();
            return isset($me['email']) ? $me['email'] : '';
        } else {
            return false;
        }
    }

    public function getNameSuggestion()
    {
        if ($this->hasCredentials()) {
            $me = $this->getMe();
            return $me['name'];
        } else {
            return false;
        }
    }

    public function getAvatarSuggestion()
    {
        if ($this->hasCredentials()) {
            $me = $this->getMe();
            $id = $me['id'];
            $image = 'http://graph.facebook.com/'. $id . '/picture?type=large';
            return $image;
        } else {
            return false;
        }
    }

    public function getUidHash()
    {
        if ($this->hasCredentials()) {
            return md5(md5($this->getProvider()->getUser()));
        } else {
            return false;
        }
    }

    public function getData()
    {
        return $this->hasCredentials() ? $this->getCredentials() : false;
    }
}