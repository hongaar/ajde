<?php

class AdminFormController extends AdminController
{
    public function view()
    {
        Ajde::app()->getDocument()->setTitle("Forms");

        return $this->render();
    }

    public function submission()
    {
        Ajde::app()->getDocument()->setTitle("Submissions");

        return $this->render();
    }
}
