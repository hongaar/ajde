<?php

class Ajde_Crud_Field_Timespan extends Ajde_Crud_Field
{
    protected function _getHtmlAttributes()
    {
        $attributes = [];
        $attributes['type'] = 'hidden';
        $attributes['value'] = Ajde_Component_String::escape($this->getValue());
        if ($this->hasReadonly() && $this->getReadonly() === true) {
            $attributes['readonly'] = 'readonly';
        }

        return $attributes;
    }
}
