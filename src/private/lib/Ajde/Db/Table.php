<?php

class Ajde_Db_Table extends Ajde_Object_Standard
{
	protected $_connection;
	protected $_name;	
	protected $_fields;
		
	public function __construct($name)
	{
		$this->_name = $name;
		$this->_connection = Ajde_Db::getInstance()->getConnection();
		$this->initTableStructure();
	}
	
	/**
	 * @return PDO
	 */
	public function getConnection()
	{
		return $this->_connection;
	}
	
	public function initTableStructure() 
	{
		$structure = Ajde_Db::getInstance()->getAdapter()->getTableStructure($this->_name);
		foreach($structure as $field) {
			$fieldName				= $field['Field'];
			$fieldType				= $field['Type'];
			$fieldParsedType		= Ajde_Db::getInstance()->getAdapter()->getFieldType($fieldType);			
			$fieldDefault			= $field['Default'];			
			$fieldLabel				= !empty($field['Comment']) ? $field['Comment'] : $field['Field'];
			
			$fieldIsRequired		= $field['Null'] === 'NO';
			$fieldIsPK				= $field['Key'] === 'PRI';
			$fieldIsAutoIncrement	= $field['Extra'] === 'auto_increment';
			$fieldIsAutoUpdate		= $field['Extra'] === 'on update CURRENT_TIMESTAMP'; 
			
			$this->_fields[$fieldName] = array(
				'name' => $fieldName,
				'dbtype' => $fieldType,
				'type' => $fieldParsedType['type'],
				'length' => $fieldParsedType['length'],
				'default' => $fieldDefault,
				'label' => $fieldLabel,
				'isRequired' => $fieldIsRequired,
				'isPK' => $fieldIsPK,				
				'isAutoIncrement' => $fieldIsAutoIncrement,
				'isAutoUpdate' => $fieldIsAutoUpdate				
			);
		}
	}
	
	public function getPK()
	{
		foreach($this->_fields as $field) {
			if ($field['isPK'] === true) {
				return $field['name'];
			}
		}
		return false;
	}
	
	public function getFK(Ajde_Db_Table $parent) {
		$fk = Ajde_Db::getInstance()->getAdapter()->getForeignKey((string) $this, (string) $parent);
		return array('field' => $fk['COLUMN_NAME'], 'parent_field' => $fk['REFERENCED_COLUMN_NAME']);		
	}
	
	public function getParents() {
		$parents = Ajde_Db::getInstance()->getAdapter()->getParents((string) $this);
		$parentTables = array();
		foreach($parents as $parent) {
			if (isset($parent['REFERENCED_TABLE_NAME'])) {
				$parentTables[] = $parent['REFERENCED_TABLE_NAME'];
			}
		}
		return $parentTables;		
	}
	
	public function getFieldProperties($fieldName = null, $property = null)
	{
		if (isset($fieldName)) {
			if (isset($this->_fields[$fieldName])) {
				if (isset($property)) {
					if (isset($this->_fields[$fieldName][$property])) {
						return $this->_fields[$fieldName][$property];
					}					
				} else {
					return $this->_fields[$fieldName];
				}
			}
		} else {
			return $this->_fields;
		}
	}
	
	public function getFieldNames()
	{
		return array_keys($this->_fields);		 
	}
	
	public function getFieldLabels()
	{
		$labels = array();
		foreach($this->_fields as $field)
		{
			$labels[$field['name']] = $field['label'];
		}		 
		return $labels;
	}
	
	public function __toString()
	{
		return $this->_name;
	}
}