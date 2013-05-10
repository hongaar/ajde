	<?php

class NodeModel extends Ajde_Model
{
	protected $_autoloadParents = true;
	protected $_displayField = 'title';
	protected $_hasMeta = true;
	
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
		$ret = _c($this->title);
		$ret = str_repeat('<span class="tree-spacer"></span>', $this->get('level')) . $ret;
		return $ret;
	}
	
	public function afterSort()
	{
		$this->sortTree('NodeCollection');
	}
	
	public function postCrudSave(Ajde_Controller $controller, Ajde_Crud $crud)
	{
		$this->sortTree('NodeCollection');
	}
	
	public function beforeSave()
	{
		// ...
	}

	public function beforeInsert()
	{
		// Added
		$this->added = new Ajde_Db_Function("NOW()");

		// Sort
//		$collection = new NodeCollection();
//		$min = 999;
//		foreach($collection as $item) {
//			$min = ($item->sort < $min) ? $item->sort : $min;
//		}
//		$this->sort = $min - 1;
		
		// Slug
		$this->_setSlug();
	}
	
	public function afterInsert()
	{
		// ...
	}
	
	private function _setSlug()
	{
		$name = $this->title;

		$ghost = new self();
		$uniqifier = 0;

		do {
			$ghost->reset();
			$slug = $this->_sluggify($name);
			$slug = $slug . ($uniqifier > 0 ? $uniqifier : '');
			$ghost->loadBySlug($slug);
			$uniqifier++;
			if ($uniqifier >= 100) {
				throw new Ajde_Controller_Exception('Max recursion depth reached for setting slug');
				break;
			}
		} while($ghost->hasLoaded());

		$this->slug = $slug;
	}

	private function _sluggify($name)
	{
		// @see http://stackoverflow.com/a/5240834
		$slug = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
		$slug = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $name);
		$slug = strtolower(trim($slug, '-'));
		$slug = preg_replace("/[\/_| -]+/", '-', $slug);
		return $slug;
	}
	
	public function loadBySlug($slug)
	{
		$this->loadByField('slug', $slug);
	}

	/***
	 * GETTERS
	 */
		
	public function getUrl()
	{
		if ($this->getPK()) {
			if (!$this->getNodetype() instanceof Ajde_Model) {
				$this->loadParents();
			}
			$nodetype = str_replace(' ', '_', strtolower($this->getNodetype()->displayField()));
			return 'http://' . Config::get('site_root') . '-' . $nodetype . '/' . $this->getSlug();
		}
		return false;
	}
	
	/**
	 * 
	 * @return NodetypeModel
	 */
	public function getNodetype()
	{
		return parent::getNodetype();
	}
	
	/**
	 * 
	 * @return MediaModel
	 */
	public function getMedia()
	{
		return parent::getMedia();
	}
	
	public function getTags()
	{
		$id = $this->getPK();
		$crossReferenceTable = 'post_tag';

		$subQuery = new Ajde_Db_Function('(SELECT tag FROM ' . $crossReferenceTable . ' WHERE post = ' . $this->getPK() . ')');
		$collection = new TagCollection();
		$collection->addFilter(new Ajde_Filter_Where('id', Ajde_Filter::FILTER_IN, $subQuery));
		
		return $collection;
	}
	
	/**
	 * 
	 * @return NodeCollection
	 */
	public function getRelatedNodes()
	{
		$collection = new NodeCollection();
		$collection->addFilter(new Ajde_Filter_Join('node_related', 'node.id', 'related'));
		$collection->addFilter(new Ajde_Filter_Where('node_related.node', Ajde_Filter::FILTER_EQUALS, $this->getPK()));
		$collection->orderBy('node_related.sort');
		
		return $collection;
	}

	public function getAdditionalMedia()
	{
		$collection = new MediaCollection();
		$collection->addFilter(new Ajde_Filter_Where('post', Ajde_Filter::FILTER_EQUALS, $this->getPK()));
		$collection->orderBy('sort');
		
		return $collection;
	}
}
