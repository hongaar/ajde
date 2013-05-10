<?php

class Ajde_Crud_Cms_Meta_Type_Listofoptions extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		$this->required();
		$this->help();
		$this->options();
		return parent::getFields();
	}
	
	public function options()
	{
		$field = $this->fieldFactory('list');
		$field->setLabel('Options');
		$field->setHelp('Each option on a different line');
		$field->setType('text');
		$field->setDisableRichText(true);
		$field->setLength(0);
		$this->addField($field);
	}
	
	public function getMetaField(MetaModel $meta)
	{
		$field = $this->decorationFactory($meta);
		$field->setType('enum');
		$field->setLength(str_replace(PHP_EOL, ',', $meta->getOption('list')));
		return $field;
	}
}