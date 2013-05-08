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
		
		$decorator = new Ajde_Crud_Cms_Meta_Decorator();
		$this->getView()->assign('decorator', $decorator);
		
		return $this->render();
	}
}