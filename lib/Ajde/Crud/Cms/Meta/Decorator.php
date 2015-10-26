<?php

class Ajde_Crud_Cms_Meta_Decorator extends Ajde_Object_Standard
{
    /**
     *
     * @var Ajde_Crud_Options
     */
    protected $options;

    protected $fields = [];

    protected $activeRow = 0;
    protected $activeColumn = 0;
    protected $activeBlock = 0;

    /**
     *
     * @var Ajde_Crud_Cms_Meta
     */
    protected $meta;

    public function __construct()
    {
        $this->meta = new Ajde_Crud_Cms_Meta();
    }

    /**
     * @return Ajde_Crud_Cms_Meta
     */
    public function getMetaObject()
    {
        return $this->meta;
    }

    public function setActiveRow($row)
    {
        $this->activeRow = $row;
    }

    public function setActiveColumn($column)
    {
        $this->activeColumn = $column;
    }

    public function setActiveBlock($block)
    {
        $this->activeBlock = $block;
    }

    public function setOptions(Ajde_Crud_Options $crudOptions)
    {
        $this->options = $crudOptions;
    }

    public function decorateOptions()
    {
        foreach ($this->meta->getFields() as $key => $field) {
            /* @var $field Ajde_Crud_Options_Fields_Field */
            $this->addField($key, $field->values());
        }
    }

    public function decorateInputs($crossReferenceTable, $crossReferenceField, $sortField, $parentField, $filters = [])
    {
        foreach ($this->meta->getMetaFields($crossReferenceTable, $crossReferenceField, $sortField, $parentField,
            $filters) as $key => $field) {
            /* @var $field Ajde_Crud_Options_Fields_Field */
            $this->addField($key, $field->values());
        }
    }

    protected function addField($key, $options)
    {
        $this->options->_stack['fields'][$key] = $options;
        $this->options->_stack['edit']['layout']['rows'][$this->activeRow]['columns'][$this->activeColumn]['blocks'][$this->activeBlock]['show'][] = $key;
    }
}
