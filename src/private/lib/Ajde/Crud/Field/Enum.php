<?php

class Ajde_Crud_Field_Enum extends Ajde_Crud_Field
{
	public function getValues()
	{
		$return = array();
		$options = explode(',', $this->getLength());
		foreach($options as $option) {
			$option = trim($option, "'");
			if ($this->hasFilter()) {
				if (!in_array($option, $this->getFilter())) {
					continue;
				}
			}
			$return[$option] = $option;
		}
		return $return;
	}
}