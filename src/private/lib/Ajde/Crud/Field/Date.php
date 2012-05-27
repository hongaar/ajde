<?php

class Ajde_Crud_Field_Date extends Ajde_Crud_Field
{
	protected function prepare()
	{
		if (
				((!$this->hasValue() || $this->isEmpty('value')) && $this->getDefault() == 'CURRENT_TIMESTAMP') ||
				($this->getIsAutoUpdate())
			) {
			$this->setReadonly(true);
		}
	}
	 
	public function setValue($value)
	{
		if ( ($this->getIsAutoUpdate())) {
			$this->set('value', null);
		} else {
			$this->set('value', $value);
		}
	}
	
	protected function _getHtmlAttributes()
	{
		$attributes = '';
		$attributes .= ' type="text" ';
		$attributes .= ' value="' . Ajde_Component_String::escape($this->getValue()) . '" ';
		if ($this->hasReadonly() && $this->getReadonly() === true) {
			$attributes .= ' readonly="readonly" ';	
		}		
		return $attributes;		
	}
}