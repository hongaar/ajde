<?php 

class TagController extends Ajde_Acl_Controller
{
	/**
	 * Default action for controller, returns the 'view.phtml' template body
	 * @return string 
	 */
	public function view()
	{
		return $this->render();
	}
	
	public function edit()
	{
		Ajde_Model::register($this);
		return $this->render();
	}
	
	public function grid()
	{
		Ajde_Model::register('portfolio');
		Ajde_Cache::getInstance()->disable();
		return $this->render();
	}

	public function statics()
	{
		Ajde_Model::register('portfolio');
		return $this->render();
	}
	
	public function refreshGrid()
	{
		Ajde_Model::register('portfolio');
		$collection = new PortfolioCollection();
		$collection
			->orderBy('sort')
			->filter('type', 'dynamic')
			->filter('published', 1);

		$collection->createGrid();
		
		Ajde_Session_Flash::alert('Grid refreshed');
		$this->redirect('admin/grid');
	}
	
	public function media()
	{
		Ajde_Model::register('portfolio');
		return $this->render();
	}
	
	public function tag()
	{
		Ajde_Model::register('portfolio');
		return $this->render();
	}

	public function mobile()
	{
		return $this->render();
	}
}