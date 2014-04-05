<?php 

class AdminFormController extends AdminController
{
	public function view()
	{
		Ajde_Model::register('form');
		
		Ajde::app()->getDocument()->setTitle("Forms");
		return $this->render();
	}

    public function submission()
    {
        Ajde_Model::register('form');

        Ajde::app()->getDocument()->setTitle("Submissions");
        return $this->render();
    }
}