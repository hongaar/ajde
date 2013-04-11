<?php

class Ajde_Crud_Field_Spatial extends Ajde_Crud_Field
{
	protected function _getHtmlAttributes()
	{
        $data = unpack('x/x/x/x/corder/Ltype/dlat/dlon', $this->getValue());        
		$attributes = '';
		$attributes .= ' type="hidden" ';
		$attributes .= ' value="'.$data['lat'].' '.$data['lon'].'" ';
		return $attributes;		
	}
}