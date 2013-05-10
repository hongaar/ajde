<?php

class Ajde_Crud_Cms_Meta_Type_Media extends Ajde_Crud_Cms_Meta_Type
{
	public function getFields()
	{
		$this->required();
		$this->help();
		return parent::getFields();
	}
	
	public function getMetaField(MetaModel $meta)
	{
		Ajde_Model::register('media');
		$field = $this->decorationFactory($meta);
		$field->setType('fk');
		$field->setModelName('media');
		$field->setUsePopupSelector(true);
		$field->setListRoute('admin/media:view.crud');
//		$field->setUseImage(true);
//		$field->addTableFileField('thumbnail', 'public/images/uploads/');
//		$field->setThumbDim(300, 20);
		return $field;
	}
}