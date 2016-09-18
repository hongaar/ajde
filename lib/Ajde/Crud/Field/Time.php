<?php

class Ajde_Crud_Field_Time extends Ajde_Crud_Field
{
    protected function _getHtmlAttributes()
    {
        $attributes = [];
        $attributes['type'] = 'time';
        if ($this->getValue()) {
            $attributes['value'] = Ajde_Component_String::escape(date('H:i', strtotime($this->getValue())));
        } else {
            $attributes['value'] = '';
        }
        if ($this->hasReadonly() && $this->getReadonly() === true) {
            $attributes['readonly'] = 'readonly';
        }

        return $attributes;
    }
}
