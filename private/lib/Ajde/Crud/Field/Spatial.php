<?php

class Ajde_Crud_Field_Spatial extends Ajde_Crud_Field
{
	protected function _getHtmlAttributes()
	{
		$value = $this->getValue();
		if (!substr_count($value, ' ')) {			
			$data = unpack('x/x/x/x/corder/Ltype/dlat/dlon', $this->getValue());      
			$value = $data['lon'].' '.$data['lat'];
		}
		$attributes = array();
		$attributes['type'] = "hidden";
		$attributes['value'] = $value;
		return $attributes;		
	}
}