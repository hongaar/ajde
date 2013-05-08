<?php

class Ajde_Crud_Cms_Meta_Type_Toggle extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		return parent::getFields();
	}
	
	public function getMetaField(MetaModel $meta)
	{
		$field = $this->decorationFactory($meta);
		$field->setType('boolean');
		return $field;
	}
}