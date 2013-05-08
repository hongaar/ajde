<?php

class Ajde_Crud_Cms_Meta_Type_Numeric extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		$this->required();
		$this->length();
		return parent::getFields();
	}
	
	public function getMetaField(MetaModel $meta)
	{
		$field = $this->decorationFactory($meta);
		$field->setType('numeric');
		return $field;
	}
}