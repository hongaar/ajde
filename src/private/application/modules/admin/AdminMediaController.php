<?php 

class AdminMediaController extends AdminController
{	
	public function view()
	{
		Ajde_Model::register('media');
		
		Ajde::app()->getDocument()->setTitle("Media");
		return $this->render();
	}
}