<?php 

class AdminEmailController extends AdminController
{
	public function view()
	{
		Ajde_Model::register('email');
		
		Ajde::app()->getDocument()->setTitle("E-mail");
		return $this->render();
	}

    public function template()
    {
        Ajde_Model::register('email');

        Ajde::app()->getDocument()->setTitle("Templates");
        return $this->render();
    }
}