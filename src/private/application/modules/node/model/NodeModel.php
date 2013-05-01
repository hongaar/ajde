	<?php

class NodeModel extends Ajde_Model
{
	protected $_autoloadParents = true;
	protected $_displayField = 'name';
	
	public function __construct() {
		Ajde_Event::register($this, 'afterCrudLoaded', array($this, 'parseForCrud'));
		parent::__construct();
	}
	
	public function beforeSave()
	{
		// ...
	}

	public function afterSort()
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
	}
	
	public function afterInsert()
	{
		// ...
	}
	
	public function parseForCrud(Ajde_Crud $crud)
	{
		// ...
	}
	
	public function hasUrl()
	{
		return true;
	}
	
	public function getUrl()
	{
		if ($this->getPK()) {
			return 'http://' . Config::get('site_root') . 'post/item/' . $this->getPK() . '.html';
		} else {
			return '(not saved)';
		}
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

	public function getMedia()
	{
		$collection = new MediaCollection();
		$collection->addFilter(new Ajde_Filter_Where('post', Ajde_Filter::FILTER_EQUALS, $this->getPK()));
		$collection->orderBy('sort');
		
		return $collection;
	}
}
