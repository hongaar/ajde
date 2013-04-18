<?php 

class AdminUserController extends AdminController
{	
	public function view()
	{
		Ajde_Model::register('user');
		
		Ajde::app()->getDocument()->setTitle("Users");
		return $this->render();
	}
}