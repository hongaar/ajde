<?php 

class AdminMenuController extends AdminController
{	
	public function view()
	{
		Ajde_Model::register($this);
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		
		Ajde::app()->getDocument()->setTitle("Menu editor");
		return $this->render();
	}
}