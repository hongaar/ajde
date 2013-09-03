<?php 

class AdminController extends Ajde_Acl_Controller
{
	/**
	 * Optional function called before controller is invoked
	 * When returning false, invocation is cancelled
	 * @return boolean 
	 */
	public function beforeInvoke($allowed = array())
	{
		Ajde::app()->getDocument()->setLayout(new Ajde_Layout(Config::get('adminLayout')));
		Ajde_Cache::getInstance()->disable();
		return parent::beforeInvoke($allowed);
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