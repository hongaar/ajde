<?php

class Ajde_Crud_Field_Boolean extends Ajde_Crud_Field
{
	protected function _getHtmlAttributes()
	{
		$attributes = array();
		$attributes['type'] = "hidden";
		$attributes['value'] = $this->getValue();
		return $attributes;
	}	
	
	public function getHtmlList($value = null)
	{
		$value = issetor($value, $this->hasValue() ? $this->getValue() : false);
		return $value ?
			"<i class='icon-ok' title='Yes' />" :
			"<i class='icon-remove' title='No' />";
			
	}
}