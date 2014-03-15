<?php 

class AdminTagController extends AdminController
{	
	public function view()
	{
		Ajde_Model::register('tag');
		
		Ajde::app()->getDocument()->setTitle("Tag manager");
		return $this->render();
	}
}