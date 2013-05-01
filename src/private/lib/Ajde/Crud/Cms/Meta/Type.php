<?php

abstract class Ajde_Crud_Cms_Meta_Type extends Ajde_Object_Standard
{	
	private $_fields = array();
	
	private function addField($name, Ajde_Crud_Options_Fields_Field $options)
	{
		if (!$options->has('label')) {
			$options->setLabel(ucfirst($name));
		}
		$this->_fields[$name] = $options;
	}
	
	public function getFields()
	{
		return $this->_fields;
	}
	
	/**
	 * 
	 * @return Ajde_Crud_Options_Fields_Field
	 */
	private function optionsFactory()
	{
		return new Ajde_Crud_Options_Fields_Field();
	}
	
	public function required()
	{
		$options = $this->optionsFactory();
		$options->setType('boolean');
		$options->setLabel('Required');
		$this->addField('required', $options);
	}
	
	public function length()
	{
		$options = $this->optionsFactory();
		$options->setType('numeric');
		$this->addField('length', $options);
	}
}