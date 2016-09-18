<?php

class AdminSetupController extends AdminController
{
    public function nodes()
    {
        Ajde::app()->getDocument()->setTitle('Setup nodes');

        return $this->render();
    }

    public function media()
    {
        Ajde::app()->getDocument()->setTitle('Setup media');

        return $this->render();
    }

    public function meta()
    {
        Ajde::app()->getDocument()->setTitle('Setup fields');

        $decorator = new Ajde_Crud_Cms_Meta_Decorator();
        $this->getView()->assign('decorator', $decorator);

        return $this->render();
    }

    public function menus()
    {
        Ajde::app()->getDocument()->setTitle('Setup menus');

        return $this->render();
    }

    public function settings()
    {
        Ajde::app()->getDocument()->setTitle('Setup settings');

        $decorator = new Ajde_Crud_Cms_Meta_Decorator();
        $this->getView()->assign('decorator', $decorator);

        return $this->render();
    }
}
