<?php

class NodeController extends Ajde_Controller
{
	public function beforeInvoke()
	{
		Ajde_Model::register('acl');
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		return true;
	}
	
	public function view()
	{
		// we want to display published nodes only
		Ajde::app()->getRequest()->set('filterPublished', true);
		
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
	
	public static function getNodeOptions()
	{
		$showOnlyWhenFields = array(
			'title', 'subtitle', 'content', 'summary', 'media', 'tag', 'additional_media', 'children', 'published', 'related_nodes'
		);
		$showOnlyWhen = array();
		$nodetypes = new NodetypeCollection();
		foreach($nodetypes as $nodetype) {
			foreach($showOnlyWhenFields as $field) {
				if (!isset($showOnlyWhen[$field])) {
					$showOnlyWhen[$field] = array();
				}
				if ($nodetype->get($field) == 1) {					
					$showOnlyWhen[$field][] = $nodetype->getPK();
				}
			}
		}

		$options = new Ajde_Crud_Options();
		$options
			->selectFields()
				->selectField('nodetype')
					->setOrderBy('sort')
					->setIsRequired(false)
					->up()
				->selectField('title')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['title'])
					->setFunction('displayTreeName')
					->setEmphasis(true)
					->up()
				->selectField('subtitle')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['subtitle'])
					->up()
				->selectField('content')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['content'])
					->up()
				->selectField('summary')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['summary'])
					->setDisableRichText(true)
					->up()
				->selectField('media')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['media'])
					->setShowLabel(false)
					->setUsePopupSelector(true)
					->setListRoute('admin/media:view.crud')
					->setUseImage(true)
					->addTableFileField('thumbnail', UPLOAD_DIR)
					->setThumbDim(300, 300)
					->up()
				->selectField('tag')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['tag'])
					->setType('multiple')
					->setEditRoute('admin/tag:view.crud')
					->setThumbDim(30, 30)
					->setShowLabel(false)
					->setCrossReferenceTable('node_tag')
					->setSimpleSelector(true)
					->up()
				->selectField('parent')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['children'])
					->setType('fk')
					->setModelName('node')
					->setShowLabel(false)
					->setUsePopupSelector(true)
					->setListRouteFunction('listRouteParent')
					->up()	
				->selectField('published')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['published'])
					->setFunction('displayPublished')
					->setType('boolean')
					->up()
				->selectField('published_start')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['published'])
					->up()
				->selectField('published_end')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['published'])
					->up()
				->selectField('sort')
					->setType('sort')
					->up()
				->selectField('url')
					->setIsReadonly(true)
					->setLabel('URL')
					->up()
				->selectField('additional_media')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['additional_media'])
					->setType('multiple')
					->setEditRoute('admin/media:view.crud')
					->addTableFileField('thumbnail', UPLOAD_DIR)
					->setHideMainColumn(true)
					->setUsePopupSelector(true)
					->setListRoute('admin/media:view.crud')
					->setModelName('media')
					->setThumbDim(100, 100)
					->addSortField('sort')
					->setShowLabel(false)
					->setCrossReferenceTable('node_media')
					->up()
				->selectField('children')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['children'])
					->setModelName('node')
					->setParent('parent')
					->setHideInIframe(true)
					->setType('multiple')
					->setEditRouteFunction('editRouteChild')
					->addTableField('nodetype')
					->addSortField('sort')
					->setShowLabel(false)
					->up()
				->selectField('related_nodes')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['related_nodes'])
					->setType('multiple')
					->setEditRoute('admin/node:view.crud')
					->setUsePopupSelector(true)
					->setListRoute('admin/node:view.crud')
					->setModelName('node')
					->setSimpleSelector(true)
					->addSortField('sort')
					->setShowLabel(false)
					->setChildField('related')
					->setCrossReferenceTable('node_related')
					->up()
				->selectField('added')
					->setIsReadonly(true)
					->up()
				->selectField('updated')
					->setFunction('displayAgo')
					->setIsReadonly(true)
					->up()
				->up()
			->selectList()
				->selectButtons()
					->setNew(true)
					->setEdit(true)
		//			->addItemButton('view', 'view')
					->addItemButton('child', 'addChildButton', 'btn-success add-child', false, true)
					->up()
				->setMain('title')
				->setShow(array('title', 'updated', 'published', 'sort'))
				->setThumbDim(50, 50)
				->setSearch(false)
				->selectView()
					->setMainFilter('nodetype')
					->setMainFilterGrouper('category')
					->setOrderBy('sort')
					->up()
				->setPanelFunction('displayPanel')
				->up()
			->selectEdit()
				->selectLayout()
					->addRow()
						->addColumn()
							->setSpan(8)
							->addBlock()
								->setShow(array('title', 'subtitle', 'content', 'summary'))
								->up()
							->addBlock()
								->setClass('')
								->up()
							->addBlock()
								->setClass('')
								->setTitle('Child nodes')
								->setShow(array('children'))
								->up()
							->up()
						->addColumn()
							->setSpan(4)
							->addBlock()
								->setTitle('Featured image')
								->setClass('sidebar well')
								->setShow(array('media'))
								->up()
							->addBlock()
								->setTitle('Tags')
								->setClass('sidebar well')
								->setShow(array('tag'))
								->up()
							->addBlock()
								->setClass('sidebar well')
								->setTitle('Additional media')
								->setShow(array('additional_media'))
								->up()
							->addBlock()
								->setClass('sidebar well')
								->setTitle('Related nodes')
								->setShow(array('related_nodes'))
								->up()
							->addBlock()
								->setTitle('Parent node')
								->setClass('sidebar well')
								->setShow(array('parent'))
								->up()
							->addBlock()
								->setTitle('Metadata')
								->setClass('narrow short well')
								->setShow(array('added', 'updated', 'published', 'published_start', 'published_end', 'user'))
		->finished();
		
		/* @var $decorator Ajde_Crud_Cms_Meta_Decorator */
		$decorator = new Ajde_Crud_Cms_Meta_Decorator();
		$decorator->setActiveBlock(1);
		$decorator->setOptions($options);
		$decorator->decorateInputs('nodetype_meta', 'nodetype', 'sort', 'nodetype', array(
			new Ajde_Filter_Where('target', Ajde_Filter::FILTER_EQUALS, 'node')
		));
				
		if (Ajde::app()->getRequest()->has('new')) {
			// set owner
			$user = UserModel::getLoggedIn();
			$options->selectFields()->selectField('user')->setValue($user->getPK())->finished();
			
			if (!UserModel::isAdmin()) {
				$currentUser = UserModel::getLoggedIn();
				$subquery = "(SELECT user_node.user FROM user_node WHERE user_node.node IN (SELECT user_node.node FROM user_node WHERE user_node.user = " . (int) $currentUser->getPK() . " GROUP BY user_node.node))";
				$userFilters = array(new Ajde_Filter_Where('user.id', Ajde_Filter::FILTER_IN, new Ajde_Db_Function($subquery)));
				$options->selectFields()->selectField('user')->setAdvancedFilter($userFilters);				
			}
		}
		if (Ajde::app()->getRequest()->has('edit')) {
			if (!UserModel::isAdmin()) {
				$options->selectFields()->selectField('user')
					->setIsReadonly(true)
					->setUsePopupSelector(true)
				->finished();
			}
		}
					
		return $options;
	}
}