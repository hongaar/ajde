<?php

class MenuModel extends Ajde_Model
{
	protected $_autoloadParents = false;
	protected $_displayField = 'name';
	
	public function __construct() {
		parent::__construct();
		$this->registerEvents();
	}
	
	public function __wakeup()
	{
		parent::__wakeup();
		$this->registerEvents();
	}
	
	public function registerEvents()
	{
		if (!Ajde_Event::has($this, 'afterCrudSave', 'postCrudSave')) {
			Ajde_Event::register($this, 'afterCrudSave', 'postCrudSave');
		}
	}
	
	public function getTreeName()
	{
		$ret = _c($this->name);
		$ret = str_repeat('<span class="tree-spacer"></span>', $this->get('level')) . $ret;
		return $ret;
	}
	
	public function afterSort()
	{
		$this->sortTree('MenuCollection');
	}
	
	public function postCrudSave(Ajde_Controller $controller, Ajde_Crud $crud)
	{
		$this->sortTree('MenuCollection');
	}
	
	public function loadByName($name)
	{
		$this->loadByField('name', $name);
	}
	
	/**
	 * 
	 * @return NodeCollection
	 */
	public function getLinks()
	{
		$collection = new NodeCollection();
		$collection->addFilter(new Ajde_Filter_Join('menu', 'menu.node', 'node.id'));
		$collection->addFilter(new Ajde_Filter_Where('menu.parent', Ajde_Filter::FILTER_EQUALS, $this->getPK()));
		$collection->orderBy('menu.sort');
		
		return $collection;
	}
	
	
}
