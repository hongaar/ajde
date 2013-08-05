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
		// load our node
		/* @var $node NodeModel */
		$slug = $this->getSlug();
		$node = $this->getModel();
		$node->loadBySlug($slug);
		
		// check if we have a hit
		if (!$node->hasLoaded()) {
			Ajde::app()->getResponse()->redirectNotFound();
		}
		
		// update cache
		Ajde_Cache::getInstance()->updateHash($node->hash());
		Ajde_Cache::getInstance()->updateHash($node->getChildren()->hash());
		
		// set title
		Ajde::app()->getDocument()->setTitle($node->getTitle());		
				
		// set template
		$nodetype = $node->getNodetype();
		$action = str_replace(' ', '_', strtolower($nodetype->get($nodetype->getDisplayField())));
		$this->setAction($action);
		
		// pass node to template
		$this->getView()->assign('node', $node);
		
		// render the temnplate
		return $this->render();
	}
}