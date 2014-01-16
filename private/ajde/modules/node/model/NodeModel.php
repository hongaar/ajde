<?php

class NodeModel extends Ajde_Model_With_AclI18n
{
	protected $_autoloadParents = false;
	protected $_displayField = 'title';
	protected $_hasMeta = true;
	
	protected $_shadowModel;
	
	public static $_parentAclCache = array();
	
	public function __construct() {
		parent::__construct();
		$this->registerEvents();
	}
	
	/**
	 * 
	 * @param int $id
	 * @return NodeModel|boolean
	 */
	public static function fromPk($id)
	{
		$node = new self();
		if ($node->loadByPK($id)) {
			return $node;
		}
		return false;
	}

    /**
     *
     * @param string $id
     * @return NodeModel|boolean
     */
    public static function fromSlug($slug)
    {
        $node = new self();
        if ($node->loadBySlug($slug)) {
            return $node;
        }
        return false;
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

    public function beforeValidate()
    {
        // required fields
        $nodetype = $this->getNodetype();
        if ($nodetype->get('required_subtitle')) { $this->addValidator('subtitle', new Ajde_Model_Validator_Required()); }
        if ($nodetype->get('required_content'))  { $this->addValidator('content', new Ajde_Model_Validator_Required()); }
        if ($nodetype->get('required_summary'))  { $this->addValidator('summary', new Ajde_Model_Validator_Required()); }
        if ($nodetype->get('required_media'))    { $this->addValidator('media', new Ajde_Model_Validator_Required()); }
        if ($nodetype->get('required_tag'))      {
            $validator = new Ajde_Model_Validator_HasChildren();
            $validator->setReferenceOptions('node_tag', 'node', 'tag');
            $this->addValidator('tag', $validator);
        }
        if ($nodetype->get('required_additional_media')) {
            $validator = new Ajde_Model_Validator_HasChildren();
            $validator->setReferenceOptions('node_media', 'node', 'media');
            $this->addValidator('additional_media', $validator);
        }
        if ($nodetype->get('required_children')) { $this->addValidator('parent', new Ajde_Model_Validator_Required()); }
        if ($nodetype->get('required_content'))  { $this->addValidator('content', new Ajde_Model_Validator_Required()); }
        if ($nodetype->get('required_related_nodes')) {
            $validator = new Ajde_Model_Validator_HasChildren();
            $validator->setReferenceOptions('node_related', 'node', 'related node');
            $this->addValidator('related_nodes', $validator);
        }

        // slug
        $this->addValidator('slug', new Ajde_Model_Validator_Unique());

        return true;
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
	
	public function getPublishData()
	{
		if ($return = $this->shadowCall('getPublishData')) {
			return $return;
		}
		return array(
				'title'		=> $this->getTitle(),
				'message'	=> $this->getSummary(),
				'image'		=> $this->getMediaAbsoluteUrl(),
				'url'		=> $this->getUrl(false)
		);
	}
	
	public function getPublishRecipients()
	{
		if ($return = $this->shadowCall('getPublishRecipients')) {
			return $return;
		}
		$users = new UserCollection();
		$addresses = array();
		foreach($users as $user) {
			/* @var $user UserModel */
			$addresses[] = $user->getEmail();
		}
		return $addresses;
	}
	
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
		$icon = $this->has('nodetype_icon') ? $this->get('nodetype_icon') : $this->getNodetype()->getIcon();
		$ret = str_repeat('<span class="tree-spacer"></span>', max(0, $this->get('level') - 1));
		if ($this->get('level') > 0) {
			$ret = $ret . '<span class="tree-spacer last"></span>';
		}
		//$ret .= '<span class="badge">'. strtolower($nodetype) . '</span>';
		$ret .= '<span class="badge-icon" title="' . _e($nodetype) . '"><i class="'. $icon . '"></i></span>';
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
	
	public function displayAgo()
	{
		$timestamp = new DateTime($this->get('updated'));
		$timestamp = $timestamp->format('U');
		return Ajde_Component_String::time2str($timestamp);
	}
	
	public function displayPublished()
	{
		if ($this->getNodetype()->get('published')) {
			if (!$this->get('published')) {
				return "<i class='icon-remove' title='No' />";
			} else if (($start = $this->get('published_start')) &&
					strtotime($start) > time()) {
				return "<i class='icon-time' title='Queued' />";
			} else if (($end = $this->get('published_end')) &&
					strtotime($end) < time()) {
				return "<i class='icon-time' title='Expired' />";
			} else {
				return "<i class='icon-ok' title='Yes' />";
			}
		} else {
			return "";
		}
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
	
	public function rowClass()
	{
		$class = strtolower($this->getNodetype()->getName());
		if ($this->has('status')) {
			$class .= ' ' . strtolower($this->get('status'));
		}
		return $class;
	}
	
	public function editRouteChild() {
		$childtype = '';
		if ($this->hasLoaded()) {
			$childtype = $this->getNodetype()->get('child_type');
		}
		return 'admin/node:view.crud?view[filter][nodetype]=' . $childtype;
	}
	
	public function listRouteParent() {
		$parenttype = '';
		if ($this->hasLoaded()) {
			$parenttype = $this->getNodetype()->get('parent_type');
		}
		return 'admin/node:view.crud?view[filter][nodetype]=' . $parenttype;
	}
	
	public function addChildButton() {
		if ($this->hasLoaded() && $childtype = $this->getNodetype()->get('child_type')) {
			$this->getNodetype()->loadParent('child_type');
			return '<i class="icon-plus icon-white" data-nodetype="' . $childtype . '"></i><span class="text-slide"> ' . strtolower($this->getNodetype()->get('child_type')->getName()) . '</span>';
		}
		return false;
	}
	
	/**
	 * BEFORE / AFTER FUNCTIONS
	 */
	
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
		$this->shadowCall('beforeDelete');
	}
	
	public function beforeSave()
	{
        // filter slug
        $this->slug = $this->_sluggify($this->slug);

		$this->shadowCall('beforeSave');	
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
		
		$this->shadowCall('beforeInsert');
	}
	
	public function afterInsert()
	{
		$this->shadowCall('afterInsert');
	}
	
	public function afterSave()
	{
		$this->shadowCall('afterSave');
	}
	
	/**
	 * Shadow model
	 */
	
	public function getShadowModel()
	{
		if (!isset($this->_shadowModel)) {
			$modelName = ucfirst($this->getNodetype()->getName()) . 'NodeModel';
			if (Ajde_Core_Autoloader::exists($modelName)) {
				$this->_shadowModel = new $modelName();
			} else {
				$this->_shadowModel = false;
			}
		}
		
		$this->shadowCopy();
		return $this->_shadowModel;
	}
	
	public function shadowCopy()
	{
		if ($this->_shadowModel) {
			$this->_shadowModel->populate($this->values());
			$this->_shadowModel->populateMeta($this->_metaValues);
		}
	}
	
	public function shadowCall($method)
	{
		$shadowModel = $this->getShadowModel();
		if ($shadowModel) {		
			try {	
				$rfmethod = new ReflectionMethod($shadowModel, $method);
				if ($rfmethod->getDeclaringClass()->getName() == get_class($shadowModel)) {			
					return $shadowModel->$method();
				}
			} catch (Exception $e) {
				return false;
			}
		}
		return false;
	}
	
	
	/**
	 * SLUG FUNCTIONS
	 */

    public function getSlug()
    {
        if (!$this->hasSlug()) {
            $this->_makeSlug();
        }
        return $this->slug;
    }

    private function _makeSlug()
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
            }
        } while($ghost->hasLoaded());

        return $slug;
    }

    /**
     * @param bool $breadcrumb
     * @deprecated use $this->slug = $this->_makeSlug();
     */
    private function _setSlug()
	{
		$this->slug = $this->_makeSlug();
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
	
	public function loadBySlug($slug, $publishedCheck = false)
	{
		$this->loadByField('slug', $slug);
		if ($publishedCheck) {
			$this->filterPublished();
		}
        return $this->hasLoaded();
	}
	
	/**
	 * PUBLISHED FUNCTIONS
	 */
	
	public function checkPublished() {
		if ($this->getNodetype()->get('published')) {
			if (!$this->get('published')) {
				return false;
			}
			if (($start = $this->get('published_start')) &&
					strtotime($start) > time()) {
				return false;
			}
			if (($end = $this->get('published_end')) &&
					strtotime($end) < time()) {
				return false;
			} 
		}
		return true;
	}
	
	public function filterPublished() {
		if (false === $this->checkPublished()) {
			$this->reset();
		}
	}
	
	protected function _load($sql, $values, $populate = true)
	{
		$return = parent::_load($sql, $values, $populate);
		if ($return && Ajde::app()->getRequest()->getParam('filterPublished', false) ==  true) {
			$this->filterPublished();
		}		
		return $return; 	
	}
	
	/**
	 * TREE FUNCTIONS
	 */
	
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
		$parent = $this->has('parent') ? $this->getParent(false) : false;
		while ($parent) {
			$lastParent = $parent;
			$node->loadByPK($parent);
			$parent = $node->has('parent') ? $node->getParent(false) : false;
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
	
	/**
	 * 
	 * @return MediaCollection
	 */
	public function getAdditionalMedia()
	{
		$collection = new MediaCollection();
		$collection->addFilter(new Ajde_Filter_Join('node_media', 'node_media.media', 'media.id'));
		$collection->addFilter(new Ajde_Filter_Join('node', 'node.id', 'node_media.node'));
		$collection->addFilter(new Ajde_Filter_Where('node_media.node', Ajde_Filter::FILTER_EQUALS, $this->getPK()));
		$collection->orderBy('node_media.sort');
	
		return $collection;
	}
	
	/**
	 *
	 * @return NodeModel
	 */
	public function getParent($load = true)
	{
		if ($load) {
			$this->loadParent('parent');
			return $this->get('parent');
		}
		return (string) $this->get('parent');
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

	/***
	 * GETTERS
	 */

    /**
     * @return bool|string
     * @deprecated
     */
    public function getPath()
	{
		if ($this->getPK()) {
			if (!$this->getNodetype() instanceof Ajde_Model) {
				$this->loadParents();
			}
			$nodetype = str_replace(' ', '_', strtolower($this->getNodetype()->displayField()));
			return '-' . $nodetype . '/' . $this->getSlug();
		}
		return false;
	}
		
	public function getUrl($relative = true)
	{
		if ($this->getPK()) {
			if (!$this->getNodetype() instanceof Ajde_Model) {
				$this->loadParents();
			}
			$nodetype = str_replace(' ', '_', strtolower($this->getNodetype()->displayField()));
//			$url = '-' . $nodetype . '/' . $this->getSlug();
            $url = $this->getFullUrl();
			return $relative ? $url : Config::get('site_root') . $url;
		}
		return false;
	}

    public function getFullUrl()
    {
        if (($parent = $this->getParent()) && $parent->hasLoaded()) {
            return $parent->getSlug() . '/' . $this->getSlug();
        }
        return $this->getSlug();
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
		if ($this->hasNotEmpty('media')) {
			return $this->getMedia()->getTag($width, $height, $crop, $class);
		}
		return '';
	}
	
	public function getMediaAbsoluteUrl()
	{
		if ($this->hasNotEmpty('media')) {
			return $this->getMedia()->getAbsoluteUrl();
		}
		return false;
	}
	
	public function getTags()
	{
		$id = $this->getPK();
		$crossReferenceTable = 'node_tag';

		$subQuery = new Ajde_Db_Function('(SELECT tag FROM ' . $crossReferenceTable . ' WHERE node = ' . $this->getPK() . ')');
		$collection = new TagCollection();
		$collection->addFilter(new Ajde_Filter_Where('id', Ajde_Filter::FILTER_IN, $subQuery));
		
		return $collection;
	}
	
	/** META **/
	
	public function getMetaValues()
	{
		if (empty($this->_metaValues)) {
			$meta = array();
			if ($this->hasLoaded()) {
				$sql = "
					SELECT node_meta.*, nodetype_meta.sort AS sort
					FROM node_meta 
					INNER JOIN nodetype_meta ON nodetype_meta.meta = node_meta.meta
						AND nodetype_meta.nodetype = ?
					WHERE node = ?
					ORDER BY sort ASC";
				$statement = $this->getConnection()->prepare($sql);
				$statement->execute(array((string) $this->getNodetype(), $this->getPK()));
				$results = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach($results as $result) {
					if (isset($meta[$result['meta']])) {
						if (is_array($meta[$result['meta']])) {
							$meta[$result['meta']][] = $result['value'];
						} else {
							$meta[$result['meta']] = array(
									$meta[$result['meta']],
									$result['value']
							);
						}
					} else {
						$meta[$result['meta']] = $result['value'];
					}
				}
			}
			$this->_metaValues = $meta;
		}
		return $this->_metaValues;
	}
}
