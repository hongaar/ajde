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
		$options->setIsRequired(true);
		$this->addField('required', $options);
	}
	
	public function length()
	{
		$options = $this->optionsFactory();
		$options->setType('numeric');
		$options->setIsRequired(true);
		$options->setDefault(255);
		$options->setHelp('hOI');
		$this->addField('length', $options);
	}
}