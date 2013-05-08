<?php

class Ajde_Crud_Cms_Meta_Type_Date extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		$this->required();
		return parent::getFields();
	}
	
	public function getMetaField(MetaModel $meta)
	{
		$field = $this->decorationFactory($meta);
		$field->setType('date');
		return $field;
	}
}