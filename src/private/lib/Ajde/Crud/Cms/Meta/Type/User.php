<?php

class Ajde_Crud_Cms_Meta_Type_User extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		$this->required();
		$this->help();
		$this->defaultValue();
		return parent::getFields();
	}
	
	public function getMetaField(MetaModel $meta)
	{
		Ajde_Model::register('user');
		$field = $this->decorationFactory($meta);
		$field->setType('fk');
		$field->setModelName('user');
		return $field;
	}
}