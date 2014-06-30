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
        // set admin layout
		Ajde::app()->getDocument()->setLayout(new Ajde_Layout(Config::get('adminLayout')));

        // disable cache and auto translations
		Ajde_Cache::getInstance()->disable();
        Ajde_Lang::getInstance()->disableAutoTranslationOfModels();

        // load all models
        Ajde_Model::registerAll();

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