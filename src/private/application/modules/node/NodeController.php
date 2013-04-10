<?php

class PostController extends Ajde_Controller
{
	protected $_allowedActions = array(
		'view', 'ajax'
	);

	public function beforeInvoke()
	{
		if (Ajde::app()->getDocument()->getTitle() == __('Untitled page')) {
			Ajde::app()->getDocument()->setTitle('Portfolio');
		}
		return true;
	}

	public function view()
    {
    	Ajde_Model::register($this);
		$posts = new PostCollection();
		$posts
			->addFilter(new Ajde_Filter_LeftJoin('media', 'post.id', 'media.post'))
			->orderBy('sort', Ajde_Query::ORDER_ASC)
			->filter('published', 1);
		$posts->getQuery()->addGroupBy('post.id');
		$posts->getQuery()->addSelect('GROUP_CONCAT( media.type SEPARATOR  \', \') AS mediatypes');

		$tags = new TagCollection();
		$tags
			->orderBy('sort', Ajde_Query::ORDER_ASC);

		$tagFilter = Ajde::app()->getRequest()->getParam('tag', false);

		if ($tagFilter) {
			$tag = new TagModel();
			$tag->loadByPK($tagFilter);
			Ajde::app()->getDocument()->setTitle($tag->name . ' - Portfolio');
		}

		Ajde_Cache::getInstance()->updateHash($posts->hash());
		Ajde_Cache::getInstance()->updateHash($tags->hash());

		$this->getView()->assign('filter', $tagFilter);
		$this->getView()->assign('posts', $posts);
		$this->getView()->assign('tags', $tags);

        return $this->render();
    }

	public function item()
    {
    	Ajde_Model::register($this);
		$item = new PortfolioModel();
		$item->loadByPK($this->getId());
		if (!$item->hasLoaded()) {
			Ajde_Http_Response::redirectNotFound();
		}

		Ajde::app()->getDocument()->setTitle($item->name . ' - Portfolio');

		Ajde_Cache::getInstance()->updateHash($item->hash());

		$this->getView()->assign('item', $item);

        return $this->render();
    }
	
	public function edit()
	{
		Ajde::app()->getDocument()->getLayout()->setAction('admin');
		
		Ajde_Model::register($this);
		Ajde_Model::register('tag');
		Ajde_Model::register('media');
		
		Ajde_Core_ExternalLibs::setOverride('Ajde_Crud_Field_Media', 'Ajde_Cms_Crud_Field_Media');
		
		return $this->render();
	}
}