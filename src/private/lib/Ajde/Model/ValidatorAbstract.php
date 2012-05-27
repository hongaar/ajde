<?php

abstract class Ajde_Model_ValidatorAbstract extends Ajde_Object_Standard
{	
	protected $_value = null;
	protected $_model = null;
	
	public function __construct(Ajde_Model $model = null)
	{
		$this->_model = $model;
	}
		
	/**
	 * Getters and setters
	 */
	
	/**
	 *
	 * @return Ajde_Model
	 */
	public function getModel()			{ return $this->_model; }
	public function setModel($model)	{ $this->_model = $model; }
	
	public function getName()			{ return parent::getName(); }
	public function getDbType()			{ return parent::getDbType(); }
	public function getLabel()			{ return parent::getLabel(); }
	public function getLength()			{ return parent::getLength(); }
	public function getIsRequired()		{ return parent::getIsRequired(); }
	public function getDefault()		{ return parent::getDefault(); }
	public function getIsAutoIncrement(){ return parent::getIsAutoIncrement(); }
	
	public function getValue()
	{
		return $this->_value;
	}
	
	public function validate($fieldOptions, $value)
	{
		$this->_value = $value;
		
		/* options */
		foreach($fieldOptions as $key => $value) {
			$this->set($key, $value);
		}
		return $this->_validate();
	}
	
	/**
	 * @return array('valid' => true|false, ['error' => (string)]);
	 */
	abstract protected function _validate();
}