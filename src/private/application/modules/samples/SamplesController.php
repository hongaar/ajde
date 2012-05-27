<?php 

class SamplesController extends Ajde_Acl_Controller
{
	protected $_allowedActions = array(
		'view',
	);
	
	public function beforeInvoke()
	{
		Ajde::app()->getDocument()->setTitle("Samples");
		return parent::beforeInvoke();
	}
		
	public function view()
    {
    	Ajde_Model::register($this);		
		// Direct object creation and chaining only from PHP 5.3!
		// Use $blog = new BlogCollection() instead		
		/* @var $samples SamplesCollection */
		$samples = SamplesCollection::create()
			->orderBy('sort', Ajde_Query::ORDER_ASC)
			->filter('published', 1);		
		if ($this->hasId()) {
			$samples->addFilter(new Ajde_Filter_Where('id', Ajde_Filter::FILTER_EQUALS, $this->getId()));
		}
		$this->getView()->assign('samples', $samples);
		Ajde_Dump::warn('This is a test warning');		
		Ajde::app()->getDocument()->setDescription("This is the samples module");
        return $this->render();
    }
		
	function edit()
	{
		Ajde_Model::register($this);
		return $this->render();
	}
	
	function xml()
	{
		$this->getView()->assign('test', "Hello World!");
		return $this->render();
	}
}