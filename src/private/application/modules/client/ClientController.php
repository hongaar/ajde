<?php 

class ClientController extends AdminController
{	
	public function beforeInvoke() {
		parent::beforeInvoke();
		
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		
		$options = NodeController::getNodeOptions();
		$this->getView()->assign('options', $options);
		
		return true;
	}
	
	public function view()
	{
		Ajde::app()->getDocument()->setTitle("<span class='page'>Clients</span>");
		return $this->render();
	}
}