<?php

class Ajde_Crud_Cms_Meta_Type_Text extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		$this->useWysiwyg();
		$this->required();
		$this->length();
		$this->help();
		$this->defaultValue();
		return parent::getFields();
	}
	
	public function useWysiwyg()
	{
		$field = $this->fieldFactory('wysiwyg');
		$field->setLabel('Use rich text editor');
		$field->setType('boolean');
		$this->addField($field);
	}
	
	public function getMetaField(MetaModel $meta)
	{
		$field = $this->decorationFactory($meta);
		if ($meta->getOption('wysiwyg')) {
			$field->setLength(0);
		} else {
			$field->setDisableRichText(true);
		}
		return $field;
	}
}