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

	protected function _getHtmlAttributes()
	{
		$attributes = array();
		if ($this->hasReadonly() && $this->getReadonly() === true) {
			$attributes['value'] = Ajde_Component_String::escape( $this->getValue() );
			$attributes['type'] = "text";
			$attributes['readonly'] = "readonly";
		} else {
			$attributes['value'] = Ajde_Component_String::escape( date('Y-m-d', strtotime($this->getValue()) ) );
			$attributes['type'] = "date";
		}
		return $attributes;
	}
}
