<?php

class NodeModel extends Ajde_Acl_Proxy_Model
{
	protected $_autoloadParents = false;
	protected $_displayField = 'title';
	protected $_hasMeta = true;
	
	public static $_parentAclCache = array();
	
	const NODETYPE_CLIENT = 23;
	const NODETYPE_PROJECT = 24;
	const NODETYPE_ISSUE = 25;
	const NODETYPE_NOTE = 27;
	const NODETYPE_STREAK = 26;
	const NODETYPE_WORK = 28;
	const NODETYPE_CONSULTATION = 29;
	const NODETYPE_SUBSCRIPTION = 30;
	
	const META_TIMESPENT = 22;
	const META_TOTALTIME = 31;
	const META_ALLOCATED = 21;
	const META_ISSUESTATUS = 23;
	const META_STREAKSTATUS = 20;
	const META_ISSUEDUE = 28;
	const META_TOTALCOST = 34;
	const META_FIXEDPRICE = 32;
	const META_DISCOUNT = 30;
	const META_BILLINGTYPE = 29;
	
	const BILLINGTYPE_FIXED = 'Fixed price';
	const BILLINGTYPE_HOURLY = 'Per hour billing';
	const BILLINGTYPE_NOTPAID = 'Not paid';
	
	const ISSUESTATUS_NEW = 'New';
	const ISSUESTATUS_ACTIVE = 'Active';
	
	const STREAKSTATUS_APPROVED = 'Approved';
	
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
			Ajde_Event::register($this, 'beforeCrudSave', 'preCrudSave');
			Ajde_Event::register($this, 'afterCrudSave', 'postCrudSave');
		}
	}
	
	public function getAclParam()
	{
		return ($this->has('nodetype') ? (string) $this->get('nodetype') : '');
	}
	
	public function validateOwner($uid, $gid)
	{
		return ((string) $this->get('user')) == $uid;
	}
	
	public function validateParent($uid, $gid)
	{
		$rootId = $this->getRoot(false);
		if (isset(self::$_parentAclCache[$rootId])) {
			$users = self::$_parentAclCache[$rootId];
		} else {		
			$root = new self();
			$root->ignoreAccessControl = true;
			$root->loadByPK($rootId);
			$users = $root->findChildUsersAsUidArray();
			self::$_parentAclCache[$rootId] = $users;
		}
		return in_array($uid, $users);
	}
	
	public function findChildUsers()
	{
		$collection = new UserCollection();
		$collection->addFilter(new Ajde_Filter_Join('user_node', 'user_node.user', 'user.id'));
		$collection->addFilter(new Ajde_Filter_Where('user_node.node', Ajde_Filter::FILTER_EQUALS, $this->getPK()));		
		return $collection;
	}
	
	public function findChildUsersAsUidArray()
	{
		$users = $this->findChildUsers();
		$ids = array();
		foreach($users as $user) {
			$ids[] = $user->_data['id'];
		}
		return $ids;
	}
	
	/**
	 * DISPLAY FUNCTIONS
	 */
	
	public function displayPanel()
	{
		$nodetype = (string) $this->get('nodetype');
		if ($nodetype == self::NODETYPE_ISSUE || $nodetype == self::NODETYPE_STREAK) {
			$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('node/row'));
			$controller->setItem($this);
			return $controller->invoke();
		}
		return false;
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
	
	public function displayParentName()
	{
		$ret = '';
		$parentId = $this->has('parent') ? $this->getParent() : false;
		if ($parentId) {
			$parent = new self();	
			$parent->ignoreAccessControl = true;
			$parent->loadByPK($parentId);			
			$ret .= '<span class="badge">'. strtolower($parent->getTitle()) . '</span>';
		}
		$ret .= ' <span class="title">' . _c($this->title) . '</span>';
		return $ret;
	}
	
	public function displayRootName()
	{
		$ret = '';
		$root = $this->findRootNoAccessChecks();
		if ($root) {		
			$ret .= '<span class="badge">'. strtolower($root->getTitle()) . '</span>';
		}
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
			
			// 5.3 only
//			$interval = $duedate->diff(new DateTime());
//			$days = -1 * (int) $interval->format('%R%a');
			
			// 5.2
			$days = round(($duedate->format('U') - $today->format('U')) / (60*60*24));;

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
		$collection->ignoreAccessControl = true;
		$collection->filterByParent($this->getPK());
		$collection->filterByType(self::NODETYPE_ISSUE);
		// TODO: filter all open types
		$collection->filterByMetaValues('issue_status', array('New', 'Active'));
		$count = $collection->count();
		return  $count > 0 ? Ajde_Component_String::makePlural($count, 'issue') : '';
	}
	
	public function displayChildrenCount()
	{
		$collection = new NodeCollection();
		$collection->ignoreAccessControl = true;
		$collection->filterByParent($this->getPK());
		$count = $collection->count();
		return $count > 0 ? Ajde_Component_String::makePlural($count, 'node') : '';
	}
	
	public function displayTimeSpent()
	{
		$timespent = $this->has('time_spent') ? $this->get('time_spent') : $this->getMetaValue('time_spent');
		return $this->span2str((int) $timespent);
	}
	
	public function displayTotalTime()
	{
		$totaltime = $this->has('total_time') ? $this->get('total_time') : $this->getMetaValue('total_time');
		return $this->span2str((int) $totaltime);
	}
	
	public function getRemaining()
	{
		$allocated = $this->has('time_allocated') ? $this->get('time_allocated') : $this->getMetaValue('time_allocated');
		$allocated = (int) $allocated;
		if ($allocated) {
			$total = $this->getTotalTime();
			return $allocated - $total;					
		}
		return false;
	}
	
	public function displayRemaining()
	{
		$remaining = $this->getRemaining();
		if ($remaining) {
			return (($remaining < 0) ? '<span class="badge badge-important">-' : '') .
				$this->span2str( abs($remaining) ) .
				(($remaining < 0) ? '<span>' : '');
		}
		return '';
	}
	
	public function rowClass()
	{
		$class = strtolower($this->getNodetype()->getName());
		if ($this->has('status')) {
			$class .= ' ' . strtolower($this->get('status'));
		}
		return $class;
	}
	
	public function afterSort()
	{
		$this->sortTree('NodeCollection');
	}
	
	public function preCrudSave(Ajde_Controller $controller, Ajde_Crud $crud)
	{
		$this->updateRoot();
	}
	
	public function postCrudSave(Ajde_Controller $controller, Ajde_Crud $crud)
	{
		// Update sort
		$this->sortTree('NodeCollection');
		
		// Update metas
		$this->updateTimeSpent();
		$this->updateTotalCost();
	}
	
	public function beforeDelete()
	{
		$this->setTimeSpent(0);
		$this->updateTimeSpent();
		$this->updateTotalCost();
	}
	
	public function beforeSave()
	{
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
	
	public function afterSave()
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
	
	/**
	 * 
	 * @param boolean $returnModel
	 * @return NodeModel|boolean
	 */
	public function getRoot($returnModel = true)
	{
		if ($this->hasNotEmpty('root')) {
			if ($returnModel) {
				$this->loadParent('root');
				return parent::getRoot();
			} else {
				return (string) parent::getRoot();
			}			
		} else {
			if ($returnModel) {
				return $this;
			} else {
				return (string) $this;
			}
		}
	}
	
	/**
	 * 
	 * @return NodeModel|boolean
	 */
	public function findRootNoAccessChecks($load = true)
	{
		return $this->findRoot(false, $load);
	}
	
	/**
	 * 
	 * @return NodeModel|boolean
	 */
	public function findRoot($accessChecks = true, $load = true)
	{
		$node = new self();
		if ($accessChecks === false) {
			$node->ignoreAccessControl = true;
		}
		$lastParent = $this->getPK();
		$parent = $this->has('parent') ? $this->getParent() : false;
		while ($parent) {
			$lastParent = $parent;
			$node->loadByPK($parent);
			$parent = $node->has('parent') ? $node->getParent() : false;
		}
		if ($lastParent === $this->getPK()) {
			return $this;
		} else if ($lastParent) {
			if ($load) {
				$root = new self();
				if (!$accessChecks) {
					$root->ignoreAccessControl = true;
				}
				$root->loadByPK($lastParent);
				return $root;
			} else {
				return (string) $lastParent;
			}
		}
		// TODO: we can never reach this?
		return false;
	}
	
	public function updateRoot()
	{
		// Update root
		$root = $this->findRootNoAccessChecks(false);
		$this->setRoot( ($this->getPK() != $root) ? $root : null );
		
		// go through all direct descendants
		$collection = new NodeCollection();
		$collection->ignoreAccessControl = true;
		$collection->autoRedirect = false;
		$collection->filterChildrenOfParent($root);
		foreach($collection as $child) {
			$child->setRoot( ($child->getPK() != $root) ? $root : null );
			$child->save();
		}
	}
	
	public function updateTimeSpent()
	{
		// find root first
		$root = $this->findRoot();

		if ($root === false) {
			return;
		}
		
		$root->resetTotalTimeRecursive();
		$root->updateTimeSpentRecursive();		
	}
	
	public function resetTotalTimeRecursive()
	{
		// go through all children
		$collection = new NodeCollection();
		$collection->filterChildrenOfParent($this->getPK());
		foreach($collection as $model) {
			/* @var $model NodeModel */
			$model->deleteMetaValue(self::META_TOTALTIME);
		}
	}
	
	public function updateTimeSpentRecursive()
	{
		$totaltime = 0;
		// go through all direct descendants
		$collection = new NodeCollection();
		$collection->filterChildrenOfParent($this->getPK(), 1);
		if ($collection->count() === 0) {
			// we reached a leaf
			$this->setTotalTime($this->getTimeSpent());
			return $this->getTimeSpent();			
		} else {
			// we found some children
			foreach($collection as $child) {
				/* @var $model NodeModel */
				$totaltime += $child->updateTimeSpentRecursive();
			}
		}
		// add own
		$totaltime += $this->getTimeSpent();
		
		// save to totaltime
		$this->setTotalTime($totaltime);
		
		// return total
		return $totaltime;
	}
	
	public function updateTotalCost()
	{
		// find root first
		$root = $this->findRoot();

		if ($root === false) {
			return;
		}
		
		$root->resetTotalCostRecursive();
		
		// go through all direct descendants
		$updateNodetypes = array(
			self::NODETYPE_STREAK
		);
		$rate = (int) SettingModel::byName('rate');
		$collection = new NodeCollection();
		$collection->filterChildrenOfParent($this->getPK());
		foreach($collection as $child) {
			/* @var $child NodeModel */
			if (in_array($child->get('nodetype'), $updateNodetypes)) {
				switch($child->getBillingType()) {
					case self::BILLINGTYPE_FIXED:
						$total = $child->getPriceFixed();
						break;
					case self::BILLINGTYPE_HOURLY:
						$total = ($child->getTotalTime() / 3600) * $rate;
						break;
					case self::BILLINGTYPE_NOTPAID:
						$total = 0;
						break;
				}
				$total = ($total - ($total * $child->getDiscount()));
				$child->setTotalCost($total);
			}
		}
	}
	
	public function resetTotalCostRecursive()
	{
		// go through all children
		$collection = new NodeCollection();
		$collection->filterChildrenOfParent($this->getPK());
		foreach($collection as $model) {
			/* @var $model NodeModel */
			$model->deleteMetaValue(self::META_TOTALCOST);
		}
	}
	
	public function getTimeAllocated()
	{
		return (int) $this->getMetaValue(self::META_ALLOCATED);
	}
	
	public function getTimeSpent()
	{
		return (int) $this->getMetaValue(self::META_TIMESPENT);
	}
	
	public function setTimeSpent($seconds)
	{
		$this->saveMetaValue(self::META_TIMESPENT, $seconds);
	}
	
	public function getTotalTime()
	{
		return (int) $this->getMetaValue(self::META_TOTALTIME);
	}
	
	public function setTotalTime($seconds)
	{
		$this->saveMetaValue(self::META_TOTALTIME, $seconds);
	}
	
	public function getDiscount()
	{
		return ((int) $this->getMetaValue(self::META_DISCOUNT) / 100);
	}
	
	public function getBillingType()
	{
		return $this->getMetaValue(self::META_BILLINGTYPE);
	}
	
	public function getPriceFixed()
	{
		return (int) $this->getMetaValue(self::META_FIXEDPRICE);
	}
	
	public function getTotalCost()
	{
		return (int) $this->getMetaValue(self::META_TOTALCOST);
	}
	
	public function setTotalCost($euros)
	{
		$this->saveMetaValue(self::META_TOTALCOST, $euros);
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
	
	public static function span2str($seconds)
	{
		if ($seconds == 0) {
			return '';
		}
		
		$span = array(
			'week' => 144000,
			'day' => 28800,
			'hour' => 3600,
			'minute' => 60
		);
	
		$weeks = floor($seconds / $span['week']);
		$seconds = $seconds - ($weeks * $span['week']);
		
		$days = floor($seconds / $span['day']);
		$seconds = $seconds - ($days * $span['day']);
		
		$hours = floor($seconds / $span['hour']);
		$seconds = $seconds - ($hours * $span['hour']);
		
		$minutes = floor($seconds / $span['minute']);
		$seconds = $seconds - ($minutes * $span['minute']);

		$output = '';
		if ($weeks) {
			$output .= $weeks . 'w ';
		}
		if ($days) {
			$output .= $days . 'd ';
		}
		if ($hours) {
			$output .= $hours . 'h ';
		}
		if ($minutes) {
			$output .= $minutes . 'm ';
		}
//		if ($seconds) {
//			$output .= $seconds . 's ';
//		}
		return trim($output);
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
