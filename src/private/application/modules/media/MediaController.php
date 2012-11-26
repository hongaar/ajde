<?php 

class MediaController extends Ajde_Acl_Controller
{	
	public function edit()
	{
		Ajde::app()->getDocument()->getLayout()->setAction('admin');
		
		Ajde_Model::register($this);
		
		return $this->render();
	}
}