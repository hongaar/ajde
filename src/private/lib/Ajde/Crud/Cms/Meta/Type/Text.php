<?php

class Ajde_Crud_Cms_Meta_Type_Text extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		$this->required();
		$this->length();
		return parent::getFields();
	}
}