<?php 

class MainController extends Ajde_Controller
{
	public function beforeInvoke()
	{
		if (substr($_GET['_route'], 0, 5) == 'admin') {
			Ajde::app()->getDocument()->setLayout(new Ajde_Layout(Config::get('adminLayout')));
		}
		return true;
	}
	
	public function view()
	{
		return $this->render();
	}
	
	public function code403()
	{
		Ajde::app()->getDocument()->setTitle("Forbidden");
		return $this->render();
	}
	
	public function code404()
	{
		Ajde::app()->getDocument()->setTitle("Not Found");
		return $this->render();
	}

	public function code500()
	{
		Ajde::app()->getDocument()->setTitle("Internal Server Error");
		return $this->render();
	}
	
	public function nojavascript()
	{
		$returnto = Ajde::app()->getRequest()->getParam('returnto', '');
		$this->getView()->assign('returnto', $returnto);
		die($this->render());
	}
	
	public function nocookies()
	{
		// set a cookie so a user can change settings in browsers which only
		// gives users the choice to enable cookies when a website tries to set one
		$session = new Ajde_Session('_ajde');
		$returnto = Ajde::app()->getRequest()->getParam('returnto', '');
		$this->getView()->assign('returnto', $returnto);
		die($this->render());
	}
	
	public function cookielaw()
	{
		return $this->render();
	}
	
	public function disclaimer()
	{
		return $this->render();
	}
}
