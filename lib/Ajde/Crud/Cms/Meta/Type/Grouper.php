<?php

class Ajde_Crud_Cms_Meta_Type_Grouper extends Ajde_Crud_Cms_Meta_Type
{
    public function getMetaField(MetaModel $meta)
    {
        $field = $this->decorationFactory($meta);
        $field->setType('header');

        return $field;
    }
}
