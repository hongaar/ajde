<?php

class Ajde_Crud_Cms_Meta_Type_Nodelink extends Ajde_Crud_Cms_Meta_Type
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
		$field = $this->fieldFactory('usenodetype');
		$field->setLabel('Node type');
		$field->setType('fk');
		$field->setIsRequired(false);
		$field->setModelName('nodetype');
		$this->addField($field);
	}
	
	public function usePopup()
	{
		$field = $this->fieldFactory('popup');
		$field->setLabel('Choose node from advanced list');
		$field->setType('boolean');
		$this->addField($field);
	}
	
	public function getMetaField(MetaModel $meta)
	{
		$field = $this->decorationFactory($meta);
		$field->setType('fk');
		$field->setModelName('node');
		if ($meta->getOption('usenodetype')) {
			$field->setAdvancedFilter(array(
				new Ajde_Filter_Where('nodetype', Ajde_Filter::FILTER_EQUALS, $meta->getOption('usenodetype'))
			));
		}
		if ($meta->getOption('popup')) {
			$field->setListRoute('admin/node:view.crud');
			$field->setUsePopupSelector(true);
		}
		return $field;
	}
}