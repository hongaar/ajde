<?php 

class MainController extends Ajde_Controller
{
	function view()
	{
		Ajde::app()->getDocument()->setTitle("Project homepage");
		return $this->render();
	}
	
	function code404()
	{
		Ajde::app()->getDocument()->setTitle("404 Not Found");
		return $this->render();
	}
	
	function nojavascript()
	{
		$returnto = Ajde::app()->getRequest()->getParam('returnto', '');
		$this->getView()->assign('returnto', $returnto);
		die($this->render());
	}
	
	function nocookies()
	{
		// set a cookie so a user can change settings in browsers which only
		// gives users the choice to enable cookies when a website tries to set one
		$session = new Ajde_Session('_ajde');
		$returnto = Ajde::app()->getRequest()->getParam('returnto', '');
		$this->getView()->assign('returnto', $returnto);
		die($this->render());
	}
}
