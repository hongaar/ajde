	<?php

class NodeModel extends Ajde_Model
{
	protected $_autoloadParents = false;
	protected $_displayField = 'title';
	protected $_hasMeta = true;
	
	const NODETYPE_CLIENT = 23;
	const NODETYPE_PROJECT = 24;
	const NODETYPE_ISSUE = 25;
	const NODETYPE_NOTE = 27;
	const NODETYPE_STREAK = 26;
	const NODETYPE_WORK = 28;
	const NODETYPE_CONSULTATION = 29;
	
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
	
	public function displayTreeName()
	{
		$nodetype = $this->has('nodetype_name') ? $this->get('nodetype_name') : $this->getNodetype()->displayField();
		$ret = str_repeat('<span class="tree-spacer"></span>', $this->get('level') - 1);
		if ($this->get('level') > 0) {
			$ret = $ret . '<span class="tree-spacer last"></span>';
		}
		$ret .= '<span class="badge">'. strtolower($nodetype) . '</span>';
		$ret .= ' <span class="title">' . _c($this->title) . '</span>';
		return $ret;
	}
	
	public function displayStatus()
	{
		if ($this->getNodetype()->getPK() == NodeModel::NODETYPE_ISSUE) {
			$status = $this->has('status') ? $this->get('status') : $this->getMetaValue('issue_status');
			$icon = '';
			$color = '';
			switch (strtolower($status)) {
				case "new":
					$icon = '';
					$color = 'info';
					break;
				case "waiting for approval":
					$icon = '';
					$color = 'warning';
					break;
				case "active":
					$icon = 'chevron-right';
					$color = 'success';
					break;
				case "closed":
					break;
			}
			return "<i class='icon-$icon'></i> <span class='badge badge-$color'>"._e($status)."</span>";
		} else if ($this->getNodetype()->getPK() == NodeModel::NODETYPE_STREAK) {
			$status = $this->has('streak_status') ? $this->get('streak_status') : $this->getMetaValue('streak_status');
			$icon = '';
			$color = '';
			switch (strtolower($status)) {
				case "in consideration":
					$icon = '';
					$color = 'info';
					break;
				case "approved":
					$icon = 'ok';
					$color = 'success';
					break;
				case "billed":
					$icon = 'check';
					$color = 'inverse';
					break;
			}
			return "<i class='icon-$icon'></i> <span class='badge badge-$color'>"._e($status)."</span>";
		}
	}
	
	public function displayDue()
	{
		$status = $this->has('issuestatus') ? $this->get('issuestatus') : $this->getMetaValue('issue_status');
		$status = strtolower($status);
		$nodetypeId = is_numeric($this->get('nodetype')) ? $this->get('nodetype') : $this->getNodetype()->getPK();
		if ($nodetypeId == NodeModel::NODETYPE_ISSUE) {
			$due = $this->has('issuedue') ? $this->get('issuedue') : $this->getMetaValue('issue_due');
			$duedate = new DateTime($due);
			$today = new DateTime();
			$today->setTime(0, 0, 0);			
			$interval = $duedate->diff(new DateTime());
			$days = -1 * (int) $interval->format('%R%a');
			$string = $this->time2str($duedate->format('U'), $today->format('U'));
			if ($status == 'new' || $status == 'active') {
				if ($days < 1) {
					return '<span class="badge badge-important">' . $string . '</span>';
				} else if ($days < 3) {
					return '<span class="badge badge-warning">' . $string . '</span>';
				}
			}
			return $string;
		}
	}
	
	public function displayOpenIssueCount()
	{
		$collection = new NodeCollection();
		$collection->filterByParent($this->getPK());
		$collection->filterByType(self::NODETYPE_ISSUE);
		// TODO: filter all open types
		$collection->filterByMetaValues('issue_status', array('New', 'Active'));
		return Ajde_Component_String::makePlural($collection->count(), 'issue');
	}
	
	public function displayChildrenCount()
	{
		$collection = new NodeCollection();
		$collection->filterByParent($this->getPK());
		return Ajde_Component_String::makePlural($collection->count(), 'node');
	}
	
	public function rowClass()
	{
		return strtolower($this->getNodetype()->getName());
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
		$this->loadParent('nodetype');
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
	
	public static function time2str($date, $today)
	{
		$diff = $today - $date;
		if($diff == 0)
			return 'now';
		elseif($diff > 0)
		{
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
				if($diff < 60) return 'just now';
				if($diff < 120) return '1 minute ago';
				if($diff < 3600) return floor($diff / 60) . ' minutes ago';
				if($diff < 7200) return '1 hour ago';
				if($diff < 86400) return floor($diff / 3600) . ' hours ago';
			}
			if($day_diff == 1) return 'yesterday';
			if($day_diff < 7) return $day_diff . ' day' . ($day_diff != 1 ? 's' : '') . ' ago';
			if($day_diff < 31) return ceil($day_diff / 7) . ' week' . (ceil($day_diff / 7) != 1 ? 's' : '') . ' ago';
			if($day_diff < 60) return 'last month';
			return date('F Y', $date);
		}
		else
		{
			$diff = abs($diff);
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
				if($diff < 120) return 'in a minute';
				if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
				if($diff < 7200) return 'in an hour';
				if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
			}
			if($day_diff == 1) return 'tomorrow';
			if($day_diff < 4) return date('l', $date);
			if($day_diff < 7 + (7 - date('w'))) return 'next week';
			if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' week' . (ceil($day_diff / 7) != 1 ? 's' : '');
			if(date('n', $date) == date('n') + 1) return 'next month';
			return date('F Y', $date);
		}
	}
}
