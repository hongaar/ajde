<?php

class NodeModel extends Ajde_Acl_Proxy_Model
{
	protected $_autoloadParents = false;
	protected $_displayField = 'title';
	protected $_hasMeta = true;
	
	public static $_parentAclCache = array();
	
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
		$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('admin/node:panel'));
		$controller->setItem($this);
		return $controller->invoke();
	}
	
	public function displayTreeName()
	{
		$nodetype = $this->has('nodetype_name') ? $this->get('nodetype_name') : $this->getNodetype()->displayField();
		$ret = str_repeat('<span class="tree-spacer"></span>', max(0, $this->get('level') - 1));
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
	}
	
	public function beforeDelete()
	{
		// ...
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
		$this->loadParent('media');
		return parent::getMedia();
	}
	
	public function getMediaTag($width = null, $height = null, $crop = null, $class = null)
	{
		if ($this->hasMedia()) {
			return $this->getMedia()->getTag($width, $height, $crop, $class);
		}
		return '';
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
	
	/**
	 *
	 * @return NodeModel
	 */
	public function getParent()
	{
		$this->loadParent('parent');
		return $this->get('parent');
	}
	
	/**
	 *
	 * @return NodeCollection
	 */
	public function getChildren()
	{
		$collection = new NodeCollection();
		$collection->filterByParent($this->getPK());
		$collection->orderBy('sort');
	
		return $collection;
	}
	
	public function getNext($loop = true)
	{
		return $this->getSibling('next', $loop);
	}
	
	public function getPrev($loop = true)
	{
		return $this->getSibling('prev', $loop);
	}
	
	public function getSibling($dir, $loop = true)
	{
		if ($dir == 'next') {
			$filter = Ajde_Filter::FILTER_GREATER;
			$order = Ajde_Query::ORDER_ASC;
		} else {
			$filter = Ajde_Filter::FILTER_LESS;
			$order = Ajde_Query::ORDER_DESC;
		}
	
		if ($this->has('parent')) {
			$siblings = new NodeCollection();
			$siblings->addFilter(new Ajde_Filter_Where('sort', $filter, $this->sort));
			$siblings->addFilter(new Ajde_Filter_Where('parent', Ajde_Filter::FILTER_EQUALS, (string) $this->get('parent')));
			$siblings->orderBy('sort', $order);
			$siblings->limit(1);
			if ($siblings->count()) {
				return $siblings->current();
			}
		}
		// Not found, loop?
		if ($loop === true) {
			$siblings->reset();
			$siblings->addFilter(new Ajde_Filter_Where('parent', Ajde_Filter::FILTER_EQUALS, (string) $this->get('parent')));
			$siblings->orderBy('sort', $order);
			$siblings->limit(1);
			if ($siblings->count()) {
				return $siblings->current();
			}
		}
		// No sibling
		return false;
	}
}
