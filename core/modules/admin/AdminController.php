<?php

class AdminController extends Ajde_Acl_Controller
{
    protected $_allowedActions = ['nav'];

    /**
     * Optional function called before controller is invoked
     * When returning false, invocation is cancelled
     *
     * @return boolean
     */
    public function beforeInvoke($allowed = [])
    {
        // set admin layout
        Ajde::app()->getDocument()->setLayout(new Ajde_Layout(config("layout.admin")));

        // disable cache and auto translations
        Ajde_Cache::getInstance()->disable();
        Ajde_Lang::getInstance()->disableAutoTranslationOfModels();

        return parent::beforeInvoke($allowed);
    }

    /**
     * Optional function called after controller is invoked
     */
    public function afterInvoke()
    {
    }

    public function nav()
    {
        return $this->render();
    }

    /**
     * Default action for controller, returns the 'view.phtml' template body
     *
     * @return string
     */
    public function view()
    {
        Ajde::app()->getDocument()->setTitle("Admin dashboard");

        return $this->render();
    }

    public function overview()
    {
        $this->setAction('view');

        return $this->view();
    }

    public function menu()
    {
        return $this->render();
    }
}
