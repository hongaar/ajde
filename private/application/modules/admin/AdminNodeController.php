<?php 

class AdminNodeController extends AdminController
{	
	public function beforeInvoke($allowed = array())
	{
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		
		return parent::beforeInvoke($allowed);
	}
	
	public function view()
	{	
		Ajde::app()->getDocument()->setTitle("Nodes");	
		return $this->render();
	}
	
	public function panel()
	{
		$item = $this->getItem();
		$this->getView()->assign('item', $item);
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
			'message' => $success ? 'Node added' : 'Something went wrong'
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
			'message' => $success ? 'Node updated' : 'Something went wrong'
		);
	}
	
}