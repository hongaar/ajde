<?php

class NodeController extends Ajde_Controller
{
	public function beforeInvoke()
	{
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		return true;
	}
	
	public function view()
	{
		$slug = $this->getSlug();
		$node = $this->getModel();
		$node->loadBySlug($slug);
		
		/* @var $node NodeModel */
		
		Ajde::app()->getDocument()->setTitle($node->getTitle());
		Ajde_Cache::getInstance()->updateHash($node->hash());
		
		$nodetype = $node->getNodetype();
		$action = str_replace(' ', '_', strtolower($nodetype->get($nodetype->getDisplayField())));
		$this->setAction($action);
		
		$this->getView()->assign('node', $node);
		return $this->render();
	}
}