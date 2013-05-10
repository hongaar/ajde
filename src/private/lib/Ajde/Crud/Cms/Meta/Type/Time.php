<?php

class Ajde_Crud_Cms_Meta_Type_Time extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		$this->required();
		$this->help();
		return parent::getFields();
	}
	
	public function getMetaField(MetaModel $meta)
	{
		$field = $this->decorationFactory($meta);
		$field->setType('time');
		return $field;
	}
}