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
		Ajde::app()->getDocument()->getLayout()->setAction('admin');
		return $this->render();
	}
	
	public function menu()
	{
		return $this->render();
	}
}