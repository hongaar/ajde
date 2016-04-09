<?php

class MediaController extends Ajde_Acl_Controller
{
    public function edit()
    {
        Ajde::app()->getDocument()->getLayout()->setAction('admin');

        return $this->render();
    }
}
