<?php

class AdminEmailController extends AdminController
{
    public function view()
    {
        Ajde::app()->getDocument()->setTitle('E-mail');

        return $this->render();
    }

    public function template()
    {
        Ajde::app()->getDocument()->setTitle('Templates');

        return $this->render();
    }

    public function history()
    {
        Ajde::app()->getDocument()->setTitle('History');

        return $this->render();
    }
}
