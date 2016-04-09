<?php

class UserSsoController extends Ajde_User_Controller
{
    protected $_allowedActions = [
        'login',
        'callback'
    ];

    /**
     * @var Ajde_User_SSO_Interface
     */
    private $_provider;
    private $_providername;

    public function beforeInvoke()
    {
        Ajde_Cache::getInstance()->disable();

        $this->_providername = Ajde::app()->getRequest()->getParam('provider', false);
        $sso                 = config('ssoProviders');
        if (!$this->_providername || !in_array($this->_providername, $sso)) {
            Ajde_Http_Response::redirectNotFound();
        }

        $classname       = 'Ajde_User_Sso_' . ucfirst($this->_providername);
        $this->_provider = new $classname;

        return parent::beforeInvoke();
    }

    public function login()
    {
        $returnto = Ajde::app()->getRequest()->getParam('returnto', '');
        $url      = $this->_provider->getAuthenticationURL($returnto);
        $this->redirect($url);
    }

    public function callback()
    {
        // from querystring?
        $returnto = Ajde::app()->getRequest()->getParam('returnto', '');
        if (empty($returnto)) {
            $returnto = Ajde_Http_Response::REDIRECT_HOMEPAGE;
        }

        // from session?
        $returntoSession = new Ajde_Session('returnto');
        if ($returntoSession->has('url')) {
            $returnto = $returntoSession->get('url');
            $returntoSession->destroy();
        }

        if (!$this->_provider->isAuthenticated()) {
            Ajde_Session_Flash::alert('Permission request cancelled for ' . ucfirst($this->_providername));
            $this->redirect($returnto);

            return false;
        }

        // We already have a user for this SSO, log that user in and redirect
        if ($user = $this->_provider->getUser()) {
            if ($this->getLoggedInUser()) {
                Ajde_Session_Flash::alert(ucfirst($this->_providername) . ' user ' . $this->_provider->getUsernameSuggestion() . ' is already connected to another account.');
                $this->redirect($returnto);
            } else {
                $user->login();
                $user->storeCookie(false);
                $this->redirect($returnto);
            }
        } else {
            // A user is already logged in, link this account and redirect
            if ($user = $this->getLoggedInUser()) {
                $sso = new SsoModel();
                $sso->populate([
                    'user'     => $user->getPK(),
                    'provider' => $this->_providername,
                    'username' => $this->_provider->getUsernameSuggestion(),
                    'avatar'   => $this->_provider->getAvatarSuggestion(),
                    'profile'  => $this->_provider->getProfileSuggestion(),
                    'uid'      => $this->_provider->getUidHash(),
                    'data'     => serialize($this->_provider->getData())
                ]);
                $sso->insert();
                $user->copyAvatarFromSso($sso);
                $this->redirect($returnto);
                // No match found, redirect to register page
            } else {
                $username = $this->_provider->getUsernameSuggestion();
                $email    = $this->_provider->getEmailSuggestion();
                $fullname = $this->_provider->getNameSuggestion();
                $this->redirect('user/register?provider=' . $this->_providername .
                    '&username=' . _e($username) .
                    '&email=' . _e($email) .
                    '&fullname=' . _e($fullname) .
                    '&hidepassword=1&returnto=' . $returnto);
            }
        }
    }

    public function disconnect()
    {
        $returnto = Ajde::app()->getRequest()->getParam('returnto', '');

        if ($user = $this->getLoggedInUser()) { // should always be true, since we are inside a Ajde_User_Controller
            $sso = new SsoModel();
            if ($sso->loadByFields([
                'user'     => $user->getPK(),
                'provider' => $this->_providername
            ])
            ) {
                $this->_provider->destroySession();
                $sso->delete();
                Ajde_Session_Flash::alert('Disconnected from ' . ucfirst($this->_providername));
                $this->redirect($returnto);
            } else {
                Ajde_Session_Flash::alert('Could not disconnect from ' . ucfirst($this->_providername));
                $this->redirect($returnto);
            }
        }
    }
}
