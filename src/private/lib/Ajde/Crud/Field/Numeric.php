<?php

class Ajde_Crud_Field_Numeric extends Ajde_Crud_Field
{
	protected function _getHtmlAttributes()
	{
		$attributes = '';
		$attributes .= ' type="number" ';
		$attributes .= ' step="any" ';
		$attributes .= ' value="' . Ajde_Component_String::escape($this->getValue()) . '" ';
		$attributes .= ' maxlength="' . $this->getLength() . '" ';
		if ($this->getIsAutoIncrement() === true) {
			$attributes .= ' readonly="readonly" ';
		}
			
		return $attributes;		
	}
}