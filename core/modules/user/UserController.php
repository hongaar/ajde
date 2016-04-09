<?php

class UserController extends Ajde_User_Controller
{
    protected $_allowedActions = [
        'forgot',
        'reset',
        'logon',
        'logoff',
        'register',
        'keepalive'
    ];
    protected $_logonRoute     = 'user/logon/html';
    protected $includeDomain   = false;

    public function beforeInvoke()
    {
        $adminAccess = false; //Ajde_Acl::validatePage('admin', '', '');
        if (
            (isset($_GET['_route']) && substr($_GET['_route'], 0, 5) == 'admin') ||
            (isset($_GET['returnto']) && substr($_GET['returnto'], 0, 5) == 'admin') ||
            $adminAccess
        ) {
            Ajde::app()->getDocument()->setLayout(new Ajde_Layout(Config::get('adminLayout')));
        }
        Ajde_Cache::getInstance()->disable();

        return parent::beforeInvoke();
    }

    // Default to profile
    public function view()
    {
        return $this->profile();
    }

    public function menu()
    {
        return $this->render();
    }

    public function app()
    {
        $user = $this->getLoggedInUser();
        $this->getView()->assign('user', $user);

        return $this->render();
    }

    public function keepaliveJson()
    {
        return ['success' => true];
    }

    // Profile
    public function profile()
    {
        $user = $this->getLoggedInUser();
        $this->setAction('profile');
        $user->refresh();
        $user->login();
        $this->getView()->assign('user', $user);

        return $this->render();
    }

    public function social()
    {
        $user = $this->getLoggedInUser();
        $this->getView()->assign('sso', Config::get('ssoProviders'));
        $this->getView()->assign('user', $user);

        return $this->render();
    }

    // Settings
    public function settingsHtml()
    {
        $user = $this->getLoggedInUser();
        $this->getView()->assign('user', $user);

        return $this->render();
    }

    public function settingsJson()
    {
        $user = $this->getLoggedInUser();

        if (!$user) {
            $return = [
                'success' => false,
                'message' => __("Not logged in")
            ];
        }

        $returnto = 'user/profile';

        $username      = Ajde::app()->getRequest()->getPostParam($user->usernameField);
        $password      = Ajde::app()->getRequest()->getPostParam('password');
        $passwordCheck = Ajde::app()->getRequest()->getPostParam('passwordCheck');
        $email         = Ajde::app()->getRequest()->getPostParam('email', false);
        $fullname      = Ajde::app()->getRequest()->getPostParam('fullname', false);

        $return = [false];

        if (empty($username)) {
            $return = [
                'success' => false,
                'message' => __("Please provide a " . $user->usernameField)
            ];
        } else {
            if (!$user->canChangeUsernameTo($username)) {
                $return = [
                    'success' => false,
                    'message' => __(ucfirst($user->usernameField) . " already exist")
                ];
            } else {
                if ($password && $password !== $passwordCheck) {
                    $return = [
                        'success' => false,
                        'message' => __("Passwords do not match")
                    ];
                } else {
                    if (empty($email)) {
                        $return = [
                            'success' => false,
                            'message' => __("Please provide an e-mail address")
                        ];
                    } else {
                        if (Ajde_Component_String::validEmail($email) === false) {
                            $return = [
                                'success' => false,
                                'message' => __('Please provide a valid e-mail address')
                            ];
                        } else {
                            if (!$user->canChangeEmailTo($email)) {
                                $return = [
                                    'success' => false,
                                    'message' => __("A user with this e-mail address already exist")
                                ];
                            } else {
                                if (empty($fullname)) {
                                    $return = [
                                        'success' => false,
                                        'message' => __("Please provide a full name")
                                    ];
                                } else {
                                    $user->set($user->usernameField, $username);
                                    $user->set('email', $email);
                                    $user->set('fullname', $fullname);
                                    if ($password) {
                                        $hash = $user->createHash($password);
                                        $user->set($user->passwordField, $hash);
                                    }
                                    if ($user->save()) {
                                        Ajde_Session_Flash::alert(__('Your settings have been saved'));
                                        $return = [
                                            'success'  => true,
                                            'returnto' => $returnto
                                        ];
                                    } else {
                                        $return = [
                                            'success' => false,
                                            'message' => __("Something went wrong")
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }

    // Logon
    public function logonHtml()
    {
        if (($user = $this->getLoggedInUser())) {
            if (($returnto = Ajde::app()->getRequest()->getParam('returnto', false))) {
                return $this->redirect($returnto);
            }
            $this->setAction('relogon');
            $message = Ajde::app()->getRequest()->getParam('message', '');
            $this->getView()->assign('message', $message);
            $this->getView()->assign('user', $user);
        } else {
            $user = new UserModel();
            $this->setAction('logon');
            //			$message = Ajde::app()->getRequest()->getParam('message', 'Please login');
            $this->getView()->assign('message', '');
            $this->getView()->assign('user', $user);
            $this->getView()->assign('returnto',
                Ajde::app()->getRequest()->getParam('returnto', $_SERVER['REDIRECT_STATUS'] == 200 ? 'user' : false));
        }
        $this->getView()->assign('sso', Config::get('ssoProviders'));

        return $this->render();
    }

    public function logonJson()
    {
        $user = new UserModel();

        $username   = Ajde::app()->getRequest()->getPostParam($user->usernameField);
        $password   = Ajde::app()->getRequest()->getPostParam('password');
        $rememberme = Ajde::app()->getRequest()->hasPostParam('rememberme');

        $return = [false];

        if (false !== $user->loadByCredentials($username, $password)) {
            $user->login();
            Ajde_Session_Flash::alert(sprintf(__('Welcome back %s'), $user->getFullname()));
            if ($rememberme === true) {
                $user->storeCookie($this->includeDomain);
            }
            $return = ['success' => true];
        } else {
            $session  = new Ajde_Session('user');
            $attempts = $session->has('attempts') ? $session->get('attempts') : 1;
            $session->set('attempts', $attempts + 1);
            if ($attempts % 4 === 0) {
                sleep(5);
            }
            $return = [
                'success' => false,
                'message' => __("We could not log you in with these credentials")
            ];
        }

        return $return;
    }

    // Reset
    public function forgotHtml()
    {
        return $this->render();
    }

    public function forgotJson()
    {
        $user = new UserModel();

        $ident  = Ajde::app()->getRequest()->getPostParam('user');
        $found  = false;
        $return = [false];

        if (false !== $user->loadByField('email', $ident)) {
            $found = true;
        }
        if (false === $found && false !== $user->loadByField($user->usernameField, $ident)) {
            $found = true;
        }

        if (false !== $found) {
            if ($user->resetUser()) {
                Ajde_Session_Flash::alert(__('A password reset link is sent to your e-mail address.'));
                $return = ['success' => true];
            } else {
                $return = [
                    'success' => false,
                    'message' => __("We could not reset your password. Please contact our technical staff.")
                ];
            }
        } else {
            $session  = new Ajde_Session('user');
            $attempts = $session->has('attempts') ? $session->get('attempts') : 1;
            $session->set('attempts', $attempts + 1);
            if ($attempts % 4 === 0) {
                sleep(5);
            }
            $return = [
                'success' => false,
                'message' => __("No matching user found")
            ];
        }

        return $return;
    }

    public function resetHtml()
    {
        $resetHash = Ajde::app()->getRequest()->getParam('h', false);
        if (!$resetHash) {
            return $this->render();
        }

        $resetArray = explode(':', $resetHash);
        $timestamp  = $resetArray[0];
        if (time() > $timestamp) {
            return $this->render();
        }

        $user  = new UserModel();
        $found = $user->loadByField('reset_hash', $resetHash);
        if (!$found) {
            return $this->render();
        }

        $user->login();

        $this->getView()->assign('user', $user);

        return $this->render();
    }

    public function resetJson()
    {
        $user = $this->getLoggedInUser();

        $password      = Ajde::app()->getRequest()->getPostParam('password');
        $passwordCheck = Ajde::app()->getRequest()->getPostParam('passwordCheck');

        $return = [false];

        $shadowUser = new UserModel();

        if (empty($password)) {
            $return = [
                'success' => false,
                'message' => __("Please provide a password")
            ];
        } else {
            if ($password !== $passwordCheck) {
                $return = [
                    'success' => false,
                    'message' => __("Passwords do not match")
                ];
            } else {
                $hash = $user->createHash($password);
                $user->set($user->passwordField, $hash);
                $user->set('secret', $user->generateSecret());
                $user->set('reset_hash', '');

                if ($user->save()) {
                    $user->login();
                    Ajde_Session_Flash::alert(sprintf(__('Welcome %s, you are now logged in'), $user->getFullname()));
                    $return = [
                        'success' => true
                    ];
                } else {
                    $return = [
                        'success' => false,
                        'message' => __("Something went wrong")
                    ];
                }
            }
        }

        return $return;
    }

    // Logoff
    public function logoff()
    {
        if (($user = $this->getLoggedInUser())) {
            $user->logout();
        }
        if (($returnto = Ajde::app()->getRequest()->getParam('returnto', false))) {
            $this->redirect($returnto);
        } elseif (substr_count(Ajde_Http_Request::getRefferer(), 'logoff') > 0 || !Ajde_Http_Request::getRefferer()) {
            $this->redirect('user');
        } else {
            $this->redirect(Ajde_Http_Response::REDIRECT_REFFERER);
        }
    }

    public function switchUser()
    {
        if (($user = $this->getLoggedInUser())) {
            $user->logout();
            $this->_user = null;
        }

        return $this->logonHtml();
    }

    public function registerHtml()
    {
        $user = new UserModel();
        $this->getView()->assign('returnto', Ajde::app()->getRequest()->getParam('returnto', false));
        $this->getView()->assign('username', Ajde::app()->getRequest()->getParam('username', false));
        $this->getView()->assign('email', Ajde::app()->getRequest()->getParam('email', false));
        $this->getView()->assign('fullname', Ajde::app()->getRequest()->getParam('fullname', false));
        $this->getView()->assign('provider', Ajde::app()->getRequest()->getParam('provider', false));
        $this->getView()->assign('hidepassword', Ajde::app()->getRequest()->getParam('hidepassword', 0));
        $this->getView()->assign('user', $user);

        return $this->render();
    }

    public function registerJson()
    {
        $user = new UserModel();

        $returnto = Ajde::app()->getRequest()->getPostParam('returnto', false);

        $username      = Ajde::app()->getRequest()->getPostParam($user->usernameField);
        $password      = Ajde::app()->getRequest()->getPostParam('password', '');
        $passwordCheck = Ajde::app()->getRequest()->getPostParam('passwordCheck', '');
        $providername  = Ajde::app()->getRequest()->getPostParam('provider', false);
        $email         = Ajde::app()->getRequest()->getPostParam('email', false);
        $fullname      = Ajde::app()->getRequest()->getPostParam('fullname', false);

        $return = [false];

        $shadowUser = new UserModel();

        $provider = false;
        if ($providername) {
            $sso = Config::get('ssoProviders');
            if (!in_array($providername, $sso)) {
                Ajde_Http_Response::redirectNotFound();
            }

            $classname = 'Ajde_User_Sso_' . ucfirst($providername);
            /* @var $provider Ajde_User_SSO_Interface */
            $provider = new $classname;
        }

        if (empty($username)) {
            $return = [
                'success' => false,
                'message' => __("Please provide a " . $user->usernameField . "")
            ];
        } else {
            if (!$provider && empty($password)) {
                $return = [
                    'success' => false,
                    'message' => __("Please provide a password")
                ];
            } else {
                if ($shadowUser->loadByField($shadowUser->usernameField, $username)) {
                    $return = [
                        'success' => false,
                        'message' => __(ucfirst($user->usernameField) . " already exist")
                    ];
                } else {
                    if (!$provider && $password !== $passwordCheck) {
                        $return = [
                            'success' => false,
                            'message' => __("Passwords do not match")
                        ];
                    } else {
                        if (empty($email)) {
                            $return = [
                                'success' => false,
                                'message' => __("Please provide an e-mail address")
                            ];
                        } else {
                            if (Ajde_Component_String::validEmail($email) === false) {
                                $return = [
                                    'success' => false,
                                    'message' => __('Please provide a valid e-mail address')
                                ];
                            } else {
                                if ($shadowUser->loadByField('email', $email)) {
                                    $return = [
                                        'success' => false,
                                        'message' => __("A user with this e-mail address already exist")
                                    ];
                                } else {
                                    if (empty($fullname)) {
                                        $return = [
                                            'success' => false,
                                            'message' => __("Please provide a full name")
                                        ];
                                    } else {
                                        if ($provider && !$provider->getData()) {
                                            $return = [
                                                'success' => false,
                                                'message' => __("Something went wrong with fetching your credentials from an external service")
                                            ];
                                        } else {
                                            $user->set('email', $email);
                                            $user->set('fullname', $fullname);
                                            if ($user->add($username, $password)) {
                                                if ($provider) {
                                                    $sso = new SsoModel();
                                                    $sso->populate([
                                                        'user'     => $user->getPK(),
                                                        'provider' => $providername,
                                                        'username' => $provider->getUsernameSuggestion(),
                                                        'avatar'   => $provider->getAvatarSuggestion(),
                                                        'profile'  => $provider->getProfileSuggestion(),
                                                        'uid'      => $provider->getUidHash(),
                                                        'data'     => serialize($provider->getData())
                                                    ]);
                                                    $sso->insert();
                                                    $user->copyAvatarFromSso($sso);
                                                }
                                                $user->login();
                                                $user->storeCookie($this->includeDomain);
                                                Ajde_Session_Flash::alert(sprintf(__('Welcome %s, you are now logged in'),
                                                    $fullname));
                                                $return = [
                                                    'success'  => true,
                                                    'returnto' => $returnto
                                                ];
                                            } else {
                                                $return = [
                                                    'success' => false,
                                                    'message' => __("Something went wrong")
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }
}
