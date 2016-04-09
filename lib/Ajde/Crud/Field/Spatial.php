<?php

class Ajde_Crud_Field_Spatial extends Ajde_Crud_Field
{
    protected function _getHtmlAttributes()
    {
        $value = $this->getValue();
        if (!substr_count($value, ' ') && !empty($value)) {
            $data  = unpack('x/x/x/x/corder/Ltype/dlat/dlon', $this->getValue());
            $value = str_replace(',', '.', $data['lat']) . ' ' . str_replace(',', '.', $data['lon']);
        }
        $attributes          = [];
        $attributes['type']  = "hidden";
        $attributes['value'] = $value;

        return $attributes;
    }
}
