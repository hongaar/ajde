<?php 

class AdminNodeController extends AdminController
{	
	public function view()
	{
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		
		Ajde::app()->getDocument()->setTitle("Nodes");
		
		return $this->render();
	}
}