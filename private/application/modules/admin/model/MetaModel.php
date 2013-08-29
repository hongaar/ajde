<?php

class MetaModel extends Ajde_Model
{
	protected $_autoloadParents = false;
	protected $_displayField = 'name';
	
	protected $_options = array();
	
	public function __construct() {
		parent::__construct();
		$this->registerEvents();
	}
	
	public function __wakeup()
	{
		parent::__wakeup();
		$this->registerEvents();
	}
	
    public function reset()
	{
		$this->_metaValues = array();
		$this->_options = array();
		parent::reset();
	}
	
	public function registerEvents()
	{
		if (!Ajde_Event::has($this, 'afterCrudLoaded', 'parseForCrud')) {
			Ajde_Event::register($this, 'afterCrudLoaded', 'parseForCrud');
			Ajde_Event::register($this, 'beforeCrudSave', 'prepareCrudSave');
		}
	}
	
	public function parseForCrud(Ajde_Crud $crud)
	{
		$options = json_decode($this->get('options'));
		foreach($options as $key => $value) {
			$this->set($key, $value);
		}	
	}
	
	public function prepareCrudSave(Ajde_Controller $controller, Ajde_Crud $crud)
	{
		$meta = new Ajde_Crud_Cms_Meta();
		$options = array();
		foreach($meta->getFieldNames() as $fieldName) {
			if ($this->has($fieldName)) {
				$options[$fieldName] = $this->get($fieldName);
			}
		}
		$this->set('options', json_encode($options));
	}
	
	public function getOption($name)
	{
		if ($this->hasLoaded() && empty($this->_options)) {
			$this->_options = (array) json_decode($this->get('options'));
		}
		if (isset($this->_options[$name])) {
			return $this->_options[$name];
		}
		return false;
	}
	
	public function getBooleanOption($name)
	{
		$val = $this->getOption($name);
		return (boolean) $val;
	}
	
	public function getIntOption($name)
	{
		$val = $this->getOption($name);
		return (int) $val;
	}
	
	public static function getNameFromId($metaId)
	{
		$meta = new self();
		$meta->loadByPK((int) $metaId);
		return $meta->displayField();
	}
}
