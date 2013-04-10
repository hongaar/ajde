<?php 

class AdminNodeController extends AdminController
{	
	public function view()
	{
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		
		Ajde::app()->getDocument()->setTitle("Nodes overview");
		return $this->render();
	}
}