<?php 

class AdminCmsController extends Ajde_Acl_Controller
{
	/**
	 * Optional function called before controller is invoked
	 * When returning false, invocation is cancelled
	 * @return boolean 
	 */
	public function beforeInvoke()
	{
		Ajde::app()->getDocument()->setLayout(new Ajde_Layout('cms'));
		return parent::beforeInvoke();
	}
	
	/**
	 * Optional function called after controller is invoked
	 */
	public function afterInvoke()
	{
		
	}
	
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
}