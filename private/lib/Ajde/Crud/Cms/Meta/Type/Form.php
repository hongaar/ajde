<?php

class Ajde_Crud_Cms_Meta_Type_Form extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		$this->required();
		$this->readonly();
		$this->help();
		$this->link();
		$this->defaultValue();
		$this->usePopup();
		return parent::getFields();
	}
	
	public function link()
	{
//		$field = $this->fieldFactory('usenodetype');
//		$field->setLabel('Node type');
//		$field->setType('fk');
//		$field->setIsRequired(false);
//		$field->setModelName('nodetype');
//		$this->addField($field);
	}
	
	public function usePopup()
	{
		$field = $this->fieldFactory('popup');
		$field->setLabel('Choose form from advanced list');
		$field->setType('boolean');
		$this->addField($field);
	}
	
	public function getMetaField(MetaModel $meta)
	{
		$field = $this->decorationFactory($meta);
		$field->setType('fk');
		$field->setModelName('form');
		if ($meta->getOption('popup')) {
			$field->setListRoute('admin/form:view.crud');
			$field->setUsePopupSelector(true);
		}
		return $field;
	}
}