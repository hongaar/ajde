<?php

class Ajde_Crud_Cms_Meta extends Ajde_Crud_Cms_Meta_Fieldlist
{
	private $_types;
	
	public function __construct() {
		
	}
	
	public function getTypes()
	{
		if (!$this->_types) {
			$ds = DIRECTORY_SEPARATOR;
			$files = Ajde_FS_Find::findFiles(LIB_DIR.'Ajde'.$ds.'Crud'.$ds.'Cms'.$ds.'Meta'.$ds.'Type'.$ds, '*.php');
			foreach($files as $file) {
				$filename = pathinfo($file, PATHINFO_FILENAME);
				$className = "Ajde_Crud_Cms_Meta_Type_" . ucfirst($filename);
				$this->_types[strtolower($filename)] = new $className();
			}
		}
		return $this->_types;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return Ajde_Crud_Cms_Meta_Type
	 * @throws Ajde_Exception
	 */
	public function getType($name)
	{
		$className = "Ajde_Crud_Cms_Meta_Type_" . ucfirst(str_replace(' ', '', strtolower($name)));
		if (!Ajde_Core_Autoloader::exists($className)) {
			// TODO:
			throw new Ajde_Exception('Meta field class ' . $className . ' could not be found'); 
		}
		return new $className();
	}
	
	public function getFields()
	{
		if (!$this->hasFields()) {
			// Reset all fields
			$this->setFields(array());

			// Iterate all available types
			foreach($this->getTypes() as $type) {
				/* @var $type Ajde_Crud_Cms_Meta_Type */

				// Iterate all fields of type
				foreach ($type->getFields() as $key => $field) {
					if ($this->hasField($key)) {
						$field = $this->getField($key);
					}
					$field->addShowOnlyWhen('type', $type->className());
					$this->setField($key, $field);
				}
			}
		}
		return parent::getFields();
	}
	
	public function getMetaFields($crossReferenceTable, $crossReferenceField, $parentField, $filters = array())
	{
		$allFields = array();
		
		Ajde_Model::register('admin');
		$metas = new MetaCollection();
		$metas->concatCrossReference($crossReferenceTable, $crossReferenceField);
		if (!empty($filters)) {
			$group = new Ajde_Filter_WhereGroup();
			foreach($filters as $filter) {
				if ($filter instanceof Ajde_Filter_Where) {
					$group->addFilter($filter);
				} else {
					$metas->addFilter($filter);
				}		
			}
			$metas->addFilter($group);
		}
		foreach($metas as $meta) {
			$metaField = $this->getType($meta->get('type'));
			$fieldOptions = $metaField->getMetaField($meta);
			foreach(explode(',', $meta->get($crossReferenceField)) as $parentValue) {
				$fieldOptions->addShowOnlyWhen($parentField, $parentValue);
			}
			$allFields['meta_' . $meta->getPK()] = $fieldOptions;
		}
		return $allFields;
	}
}