<?php

abstract class Ajde_Crud_Cms_Meta_Type extends Ajde_Crud_Cms_Meta_Fieldlist
{
    private $_forbiddenFieldNames = [
        'values',
        'options',
        'type'
    ];

    protected $niceName = false;

    public function className()
    {
        $className = get_class($this);

        return strtolower(substr($className, strrpos($className, '_') + 1));
    }

    public function niceName()
    {
        return $this->niceName ? $this->niceName : ucfirst($this->className());
    }

    /**
     * Gets called before model save of this meta type
     *
     * @return boolean
     */
    public function beforeSave(MetaModel $meta, $value, Ajde_Model $model)
    {
        return $value;
    }

    /**
     * Gets called after model save of this meta type
     */
    public function afterSave(MetaModel $meta, $value, Ajde_Model $model)
    {
        // ...
    }

    /**
     *
     * @return Ajde_Crud_Options_Fields_Field
     */
    protected function fieldFactory($name)
    {
        if (in_array($name, $this->_forbiddenFieldNames)) {
            throw new Ajde_Exception('The field name \'' . $name . '\' can not be used');
        }
        if ($this->hasField($name)) {
            $field = $this->getField($name);
        } else {
            $field = new Ajde_Crud_Options_Fields_Field();
            $field->setName($name);
            $field->setLabel(ucfirst($name));
        }

        return $field;
    }

    /**
     *
     * @param MetaModel $meta
     * @return Ajde_Crud_Options_Fields_Field
     */
    protected function decorationFactory(MetaModel $meta)
    {
        $field = new Ajde_Crud_Options_Fields_Field();
        $field->setName('meta_' . $meta->getPK());
        $field->setType('text');
        $field->setLabel($meta->get('name'));
        if ($meta->getOption('help')) {
            $field->setHelp($meta->getOption('help'));
        }
        if ($meta->getOption('default')) {
            $field->setDefault($meta->getOption('default'));
        }
        if ($meta->getOption('readonly')) {
            $field->setIsReadonly(true);
        }
        $field->setLength($meta->getIntOption('length'));
        $field->setIsRequired($meta->getBooleanOption('required'));

        return $field;
    }

    /**
     *
     * @param MetaModel $meta
     * @return Ajde_Crud_Options_Fields_Field
     */
    public function getMetaField(MetaModel $meta)
    {
        $field = $this->decorationFactory($meta);

        return $field;
    }

    public function required()
    {
        $field = $this->fieldFactory('required');
        $field->setType('boolean');
        $this->addField($field);
    }

    public function length()
    {
        $field = $this->fieldFactory('length');
        $field->setType('numeric');
        $field->setDefault(255);
        $this->addField($field);
    }

    public function help()
    {
        $field = $this->fieldFactory('help');
        $field->setType('text');
        $field->setLength(255);
        $this->addField($field);
    }

    public function defaultValue()
    {
        $field = $this->fieldFactory('default');
        $field->setType('text');
        $field->setLength(255);
        $this->addField($field);
    }

    public function readonly()
    {
        $field = $this->fieldFactory('readonly');
        $field->setType('boolean');
        $this->addField($field);
    }
}
