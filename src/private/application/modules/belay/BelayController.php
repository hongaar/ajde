<?php

class BelayController extends AdminController
{
	public function view()
	{
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		
		$options = NodeController::getNodeOptions();
		$this->getView()->assign('options', $options);
		
		Ajde::app()->getDocument()->setTitle("What's your issue?");
		return $this->render();
	}
}