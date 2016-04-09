<?php

class AdminTagController extends AdminController
{
    public function view()
    {
        Ajde::app()->getDocument()->setTitle("Tag manager");

        return $this->render();
    }
}
