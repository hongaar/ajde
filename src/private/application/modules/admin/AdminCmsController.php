<?php 

class AdminCmsController extends AdminController
{
	/**
	 * Default action for controller, returns the 'view.phtml' template body
	 * @return string 
	 */
	public function view()
	{
		Ajde::app()->getDocument()->setTitle("CMS");
		return $this->render();
	}
	
	public function menu()
	{
		return $this->render();
	}
	
	public function setupmenu()
	{
		return $this->render();
	}
	
	public function nodes()
	{
		Ajde::app()->getDocument()->setTitle("Nodes");
		return $this->render();
	}
	
	public function media()
	{
		Ajde::app()->getDocument()->setTitle("Media");
		return $this->render();
	}
	
	public function menus()
	{
		Ajde::app()->getDocument()->setTitle("Menus");
		return $this->render();
	}
	
	public function tags()
	{
		Ajde::app()->getDocument()->setTitle("Tags");
		return $this->render();
	}
}