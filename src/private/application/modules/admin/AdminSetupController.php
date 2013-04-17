<?php 

class AdminSetupController extends AdminController
{	
	public function types()
	{
		Ajde_Model::register($this);
		
		Ajde::app()->getDocument()->setTitle("Node types");
		return $this->render();
	}
	
	public function meta()
	{
		Ajde_Model::register($this);
		
		Ajde::app()->getDocument()->setTitle("Meta keys");
		return $this->render();
	}
}