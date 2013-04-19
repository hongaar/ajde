<?php 

class AdminController extends Ajde_Acl_Controller
{
	/**
	 * Optional function called before controller is invoked
	 * When returning false, invocation is cancelled
	 * @return boolean 
	 */
	public function beforeInvoke()
	{
		Ajde::app()->getDocument()->setLayout(new Ajde_Layout('cms'));
		Ajde_Cache::getInstance()->disable();
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
		Ajde::app()->getDocument()->setTitle("Admin dashboard");
		return $this->render();
	}
	
	public function menu()
	{
		return $this->render();
	}
}