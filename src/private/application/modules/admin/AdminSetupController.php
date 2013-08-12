<?php 

class AdminSetupController extends AdminController
{	
	public function nodes()
	{
		Ajde_Model::register($this);
		
		Ajde::app()->getDocument()->setTitle("Setup nodes");
		return $this->render();
	}
	
	public function media()
	{
		Ajde_Model::register($this);
	
		Ajde::app()->getDocument()->setTitle("Setup media");
		return $this->render();
	}
	
	public function meta()
	{
		Ajde_Model::register($this);
		
		Ajde::app()->getDocument()->setTitle("Setup fields");
		
		$decorator = new Ajde_Crud_Cms_Meta_Decorator();
		$this->getView()->assign('decorator', $decorator);
		
		return $this->render();
	}
	
	public function menus()
	{
		Ajde_Model::register($this);
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		
		Ajde::app()->getDocument()->setTitle("Setup menus");
		return $this->render();
	}
	
	public function settings()
	{
		Ajde_Model::register($this);
		
		Ajde::app()->getDocument()->setTitle("Setup settings");
		
		$decorator = new Ajde_Crud_Cms_Meta_Decorator();
		$this->getView()->assign('decorator', $decorator);
		
		return $this->render();
	}	
}