<?php 

class IssueController extends AdminController
{	
	public function beforeInvoke() {
		parent::beforeInvoke();
		
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		
		$options = NodeController::getNodeOptions();
		if ($this->getFormat() !== 'json') {
			// Not in json mode
			$this->getView()->assign('options', $options);
		}
		
		return true;
	}
	
	public function view()
	{				
		$parentId = false;		
		$session = new Ajde_Session('belay');		
		if ($parentId = Ajde::app()->getRequest()->getParam('parent', false)) {
			$session->set('issue_parent', $parentId);
		} else if ($session->has('issue_parent')) {
			$parentId = $session->get('issue_parent');
		} else {
			Ajde_Session_Flash::alert('Please choose a project first');
			$this->redirect('project');
			return false;
		}
		
		$parent = new NodeModel();
		$parent->loadByPK($parentId);
		Ajde::app()->getDocument()->setTitle("<span class='page'>Issues</span> " . _e($parent->displayField()));
		
		$this->getView()->assign('parent', $parentId);
		return $this->render();
	}
	
	public function quickJson()
	{
		$parent = Ajde::app()->getRequest()->getPostParam('parent');
		$title = Ajde::app()->getRequest()->getPostParam('title');
		$due = Ajde::app()->getRequest()->getPostParam('due');
		$allocated = Ajde::app()->getRequest()->getPostParam('allocated');
		
		$model = new NodeModel();
		
		$model->populate(array(
			'parent' => $parent,
			'title' => $title,
			'user' => UserModel::getLoggedIn()->getPK(),
			'nodetype' => NodeModel::NODETYPE_ISSUE
		));
		
		Ajde_Event::trigger($model, 'beforeCrudSave', array());
		$success = $model->insert();
		Ajde_Event::trigger($model, 'afterCrudSave', array());
		
		$model->saveMetaValue(NodeModel::META_ISSUESTATUS, NodeModel::ISSUESTATUS_NEW);
		$model->saveMetaValue(NodeModel::META_ISSUEDUE, $due);
		$model->saveMetaValue(NodeModel::META_ALLOCATED, $allocated);
		
		return array(
			'success' => $success,
			'message' => $success ? 'Issue added' : 'Something went wrong'
		);
	}
	
	public function updateJson()
	{		
		$id = Ajde::app()->getRequest()->getParam('id');
		
		$meta = Ajde::app()->getRequest()->getPostParam('meta');
		$key = Ajde::app()->getRequest()->getPostParam('key');
		$value = Ajde::app()->getRequest()->getPostParam('value');
	
		$model = new NodeModel();
		
		$model->loadByPK($id);
		$success = false;
		if ($meta) {
			$model->saveMetaValue($key, $value);
			$success = true;
		} else {
			$model->set($key, $value);
			$success = $model->save();
		}
	
		return array(
			'success' => true,
			'message' => $success ? 'Issue updated' : 'Something went wrong'
		);
	}
	
	public function statusBtn()
	{
		return $this->render();
	}
	
	public function statusBtnJson()
	{		
		$value = Ajde::app()->getRequest()->getPostParam('status');
		$id = Ajde::app()->getRequest()->getPostParam('id', false);
		
		$model = new NodeModel();
		
		if (!is_array($id)) {
			$id = array($id);
		}
		
		foreach($id as $elm) {
			$model->loadByPK($elm);
			$model->saveMetaValue(NodeModel::META_ISSUESTATUS, $value);
		}
		
		return array(
			'success' => true,
			'message' => Ajde_Component_String::makePlural(count($id), 'issues') . ' changed'
		);
	}
}