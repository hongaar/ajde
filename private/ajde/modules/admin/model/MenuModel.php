<?php

class MenuModel extends Ajde_Model_With_I18n
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
	
	public function displayLang()
	{
		$lang = Ajde_Lang::getInstance();
		$currentLang = $this->get('lang');
		if ($currentLang) {
			return $lang->getNiceName($currentLang);
		}
		return '';
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
		$collection->getQuery()->addSelect('menu.name as name');
		$collection->orderBy('menu.sort');
		
		return $collection;
	}
	
	public function getItems()
	{
		$collection = new MenuCollection();
		$collection->filterByParent($this->id);
		$collection->orderBy('sort');
		
		$node = new NodeModel();
		
		$items = array();
		foreach($collection as $item) {
			$name = $item->name;
			$target = "";
			if ($item->type == 'URL') {				
				$url = $item->url;
				if (substr($url, 0, 7) === 'http://' || substr($url, 0, 8) === 'https://') {
					$target = "_blank";
				}
			} else if ($item->type == 'Node link') {
				$node->loadByPK($item->node);
				$url = $node->getUrl();				
			}
			$items[] = array(
				'name' => $name,
				'url' => $url,
				'target' => $target			
			);
		}
		
		return $items;
	}
	
	
}
