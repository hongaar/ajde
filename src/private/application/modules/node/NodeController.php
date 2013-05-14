<?php

class NodeController extends Ajde_Controller
{
	public function beforeInvoke()
	{
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		return true;
	}
	
	public function view()
	{
		$slug = $this->getSlug();
		$node = $this->getModel();
		$node->loadBySlug($slug);
		
		/* @var $node NodeModel */
		
		Ajde::app()->getDocument()->setTitle($node->getTitle());
		Ajde_Cache::getInstance()->updateHash($node->hash());
		
		$nodetype = $node->getNodetype();
		$action = str_replace(' ', '_', strtolower($nodetype->get($nodetype->getDisplayField())));
		$this->setAction($action);
		
		$this->getView()->assign('node', $node);
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
				if ($nodetype->get($field) == 1) {
					if (!isset($showOnlyWhen[$field])) {
						$showOnlyWhen[$field] = array();
					}
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
					->addTableFileField('thumbnail', 'public/images/uploads/')
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
					->setListRoute('admin/node:view.crud')
					->up()	
				->selectField('published')
					->addShowOnlyWhen('nodetype', $showOnlyWhen['published'])
					->setType('boolean')
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
					->addTableFileField('thumbnail', 'public/images/uploads/')
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
					->setEditRoute('admin/node:view.crud')
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
				->up()
			->selectList()
				->selectButtons()
					->setNew(true)
					->setEdit(true)
		//			->addItemButton('view', 'view')
					->up()
				->setMain('title')
				->setShow(array('title', 'added', 'published', 'sort'))
				->setThumbDim(50, 50)
				->selectView()
					->setMainFilter('nodetype')
					->setMainFilterGrouper('category')
					->setOrderBy('sort')
					->up()
				->up()
			->selectEdit()
				->setShow()
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
								->setShow(array('added', 'updated', 'published', 'user'))
		->finished();
		
		/* @var $decorator Ajde_Crud_Cms_Meta_Decorator */
		$decorator = new Ajde_Crud_Cms_Meta_Decorator();
		$decorator->setActiveBlock(1);
		$decorator->setOptions($options);
		$decorator->decorateInputs('nodetype_meta', 'nodetype', 'nodetype', array(
			new Ajde_Filter_Where('target', Ajde_Filter::FILTER_EQUALS, 'node')
		));
		
		return $options;
	}
}