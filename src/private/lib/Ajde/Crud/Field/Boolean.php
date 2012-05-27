<?php

class Ajde_Crud_Field_Boolean extends Ajde_Crud_Field
{
	protected function _getHtmlAttributes()
	{
		$attributes = '';
		$attributes .= ' type="hidden" ';
		$attributes .= ' value="'.$this->getValue().'" ';
		return $attributes;		
	}	
	
	public function getHtmlList($value = null)
	{
		$value = issetor($value, $this->hasValue() ? $this->getValue() : false);
		return $value ?
			" <img src='public/images/icons/16/flag_mark_green.png' style='vertical-align: middle;' title='True' />" :
			" <img src='public/images/icons/16/flag_mark_red.png' style='vertical-align: middle;' title='False' />";
			
	}
}