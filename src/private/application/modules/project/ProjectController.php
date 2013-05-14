<?php 

class ProjectController extends AdminController
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
		$parentId = false;		
		$session = new Ajde_Session('belay');		
		if ($parentId = Ajde::app()->getRequest()->getParam('parent', false)) {
			$session->set('project_parent', $parentId);
		} else if ($session->has('project_parent')) {
			$parentId = $session->get('project_parent');
		} else {
			Ajde_Session_Flash::alert('Please choose a client first');
			$this->redirect('client');
			return false;
		}
		
		$parent = new NodeModel();
		$parent->loadByPK($parentId);
		Ajde::app()->getDocument()->setTitle("<span class='page'>Projects</span> " . _e($parent->displayField()));
		
		$this->getView()->assign('parent', $parentId);
		return $this->render();
	}
}