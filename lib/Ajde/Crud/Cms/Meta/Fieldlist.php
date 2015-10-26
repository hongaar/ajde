<?php

abstract class Ajde_Crud_Cms_Meta_Fieldlist extends Ajde_Object_Standard
{
    private $_fields = [];

    protected function addField(Ajde_Crud_Options_Fields_Field $field)
    {
        $this->_fields[$field->getName()] = $field;
    }

    public function getFields()
    {
        return $this->_fields;
    }

    public function hasField($name)
    {
        return isset($this->_fields[$name]);
    }

    public function hasFields()
    {
        return !empty($this->_fields);
    }

    public function getField($name)
    {
        return $this->_fields[$name];
    }

    public function setField($name, $field)
    {
        $this->_fields[$name] = $field;
    }

    public function setFields($fields)
    {
        $this->_fields = $fields;
    }

    public function getFieldNames()
    {
        return array_keys($this->getFields());
    }
}
