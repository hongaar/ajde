<?php 

class AdminCmsController extends AdminController
{
	/**
	 * Default action for controller, returns the 'view.phtml' template body
	 * @return string 
	 */
	public function view()
	{
		Ajde::app()->getDocument()->setTitle("CMS");
		return $this->render();
	}
	
	public function menu()
	{
		return $this->render();
	}
	
	public function nav()
	{
		return $this->render();
	}
	
	public function navJson()
	{
		$parent = Ajde::app()->getRequest()->getInt('node', false);
		
		$nodes = new NodeCollection();		
		if ($parent) {
			$nodes->addFilter(new Ajde_Filter_Where('parent', Ajde_Filter::FILTER_EQUALS, $parent));
		} else {
			$nodes->addFilter(new Ajde_Filter_Where('level', Ajde_Filter::FILTER_EQUALS, 0));
		}
		$nodes->getQuery()->addSelect('id AS aid');
		$nodes->getQuery()->addSelect('(SELECT count(b.id) FROM node b WHERE b.parent = aid) AS children');
		$nodes->orderBy('sort');

		$json = array();
		foreach($nodes as $node) {
			/* @var $node NodeModel */
			$children = $node->get('children');
			$json[] = array(
				"label" => $node->getTitle(),
				"id" => $node->getPK(),
				"load_on_demand" => $children ? true : false
					);
		}
		
		return $json;
	}
	
	public function setupmenu()
	{
		return $this->render();
	}
	
	public function nodes()
	{
		Ajde::app()->getDocument()->setTitle("Nodes");
		return $this->render();
	}
	
	public function media()
	{
		Ajde::app()->getDocument()->setTitle("Media");
		return $this->render();
	}
	
	public function menus()
	{
		Ajde::app()->getDocument()->setTitle("Menus");
		return $this->render();
	}
	
	public function tags()
	{
		Ajde::app()->getDocument()->setTitle("Tags");
		return $this->render();
	}
	
	public function settings()
	{
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		
		Ajde::app()->getDocument()->setTitle("Settings");
		
		$decorator = new Ajde_Crud_Cms_Meta_Decorator();
		$this->getView()->assign('decorator', $decorator);
		
		return $this->render();
	}
}