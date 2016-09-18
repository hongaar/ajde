<?php

class Ajde_Crud_Field_Boolean extends Ajde_Crud_Field
{
    protected function _getHtmlAttributes()
    {
        $attributes = [];
        $attributes['type'] = 'hidden';
        $attributes['value'] = $this->getValue() ? '1' : '0';

        return $attributes;
    }

    public function getHtmlList($value = null)
    {
        $value = issetor($value, $this->hasValue() ? $this->getValue() : false);

        return $value ?
            "<i class='icon-ok' title='Yes'></i>" :
            "<i class='icon-remove' title='No'></i>";
    }
}
