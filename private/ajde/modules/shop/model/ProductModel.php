<?php

class ProductModel extends Ajde_Model
{
	protected $_autoloadParents = true;
	protected $_displayField = 'title';
	
	public function __construct() {
		Ajde_Event::register($this, 'afterCrudLoaded', array($this, 'parseForCrud'));
		parent::__construct();
	}
	
	public function beforeSave()
	{
		if (!$this->isEncrypted('encrypted_field')) {
			$this->encrypt('encrypted_field');
		}
	}

	public function getVATPercentage()
	{
		if ($this->hasVat() && !$this->getVat() instanceof Ajde_Model) {
			$this->loadParents();
		}
		return $this->hasVat() ? ((float) $this->getVat()->getPercentage() / 100) : 0;
	}
	
	public function parseForCrud(Ajde_Crud $crud)
	{
		// Do something
	}
	
	public function hasUrl()
	{
		return true;
	}
	
	public function getUrl()
	{
		if ($this->getPK()) {
			return Config::get('site_root') . 'sample/' . $this->getPK() . '.html';
		} else {
			return '(not saved)';
		}
	}	
}
