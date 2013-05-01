<?php

class Ajde_Crud_Cms_Meta extends Ajde_Object_Standard
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
}