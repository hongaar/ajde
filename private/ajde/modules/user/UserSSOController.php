<?php 

class UserSSOController extends Ajde_User_Controller
{
	protected $_allowedActions = array(
		'login',
        'callback'
	);

    /**
     * @var Ajde_User_SSO
     */
    private $_provider;
    private $_providername;

    public function beforeInvoke()
    {
        Ajde_Cache::getInstance()->disable();

        $this->_providername = Ajde::app()->getRequest()->getParam('provider', false);
        $sso = Config::get('ssoProviders');
        if (!$this->_providername || !in_array($this->_providername, $sso)) {
            Ajde_Http_Response::redirectNotFound();
        }

        $classname = 'Ajde_User_SSO_' . ucfirst($this->_providername);
        $this->_provider = new $classname;

        return parent::beforeInvoke();
    }

    public function login()
    {
        $returnto = Ajde::app()->getRequest()->getParam('returnto', '');
        $url = $this->_provider->getAuthenticationURL($returnto);
        $this->redirect($url);
    }

    public function callback()
    {
        $returnto = Ajde::app()->getRequest()->getParam('returnto', '');
        $this->_provider->getAccessToken();
        if ($user = $this->_provider->getUser()) {
            $user->login();
            $this->redirect($returnto);
        } else {
            if ($user = $this->getLoggedInUser()) {
                $sso = new SsoModel();
                $sso->populate(array(
                    'user' => $user->getPK(),
                    'provider' => $this->_providername,
                    'token' => md5($this->_provider->getToken()),
                    'data' => serialize($this->_provider->getData())
                ));
                $sso->insert();
                $this->redirect($returnto);
            } else {
                $username = $this->_provider->getUsernameSuggestion();
                $this->redirect('user/register?provider=' . $this->_providername . '&username=' . $username . '&hidepassword=1&returnto=' . $returnto);
            }
        }
    }
}
