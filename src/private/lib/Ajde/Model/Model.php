<?php

class Ajde_Model extends Ajde_Object_Standard
{	
	protected $_connection;
	protected $_table;
	
	protected $_autoloadParents = false;
	protected $_displayField = null;
	
	protected $_validators = array();
	
	public static function register($controller)
	{
		// Extend Ajde_Controller
		if (!Ajde_Event::has('Ajde_Controller', 'call', 'Ajde_Model::extendController')) {
			Ajde_Event::register('Ajde_Controller', 'call', 'Ajde_Model::extendController');
		}
		// Extend autoloader
		if ($controller instanceof Ajde_Controller) {
			Ajde_Core_Autoloader::addDir(MODULE_DIR.$controller->getModule().'/model/');
		} elseif ($controller === '*') {
			self::registerAll();
		} else {
			Ajde_Core_Autoloader::addDir(MODULE_DIR.$controller.'/model/');
		}		
	}
	
	public static function registerAll()
	{
		$dirs = Ajde_FS_Find::findFiles(MODULE_DIR, '*/model');
		foreach($dirs as $dir) {
			Ajde_Core_Autoloader::addDir($dir . '/');
		}		
	}
	
	public static function extendController(Ajde_Controller $controller, $method, $arguments)
	{
		// Register getModel($name) function on Ajde_Controller
		if ($method === 'getModel') {
			self::register($controller);
			if (!isset($arguments[0])) {
				$arguments[0] = $controller->getModule();
			}			
			return self::getModel($arguments[0]);
		}
		// TODO: if last triggered in event cueue, throw exception
		// throw new Ajde_Exception("Call to undefined method ".get_class($controller)."::$method()", 90006);
		// Now, we give other callbacks in event cueue chance to return
		return null;  
	}
	
	/**
	 *
	 * @param string $name
	 * @return Ajde_Model
	 */
	public static function getModel($name)
	{
		$modelName = ucfirst($name) . 'Model';
		return new $modelName();
	}
	
	public function __construct()
	{
		$tableNameCC = str_replace('Model', '', get_class($this));
		$tableName = $this->fromCamelCase($tableNameCC);

		$this->_connection = Ajde_Db::getInstance()->getConnection();	
		$this->_table = Ajde_Db::getInstance()->getTable($tableName);		
	}
	
	public function __set($name, $value) {
        $this->set($name, $value);
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __isset($name) {
        return $this->has($name);
    }
	
	public function __toString() {
		if (empty($this->_data)) {
			return null;
		}
		return $this->getPK();		
	}
	
	public function __sleep()
	{
		return array('_autoloadParents', '_displayField', '_data');
	}

	public function __wakeup()
	{
		$tableNameCC = str_replace('Model', '', get_class($this));
		$tableName = $this->fromCamelCase($tableNameCC);
		
		$this->_connection = Ajde_Db::getInstance()->getConnection();	
		$this->_table = Ajde_Db::getInstance()->getTable($tableName);	
	}

	public function isEmpty($key)
	{
		$value = (string) $this->get($key);
		return empty($value);
	}
	
	public function getPK()
	{
		if (empty($this->_data)) {
			return null;
		}
		return $this->get($this->getTable()->getPK()); 
	}
	
	public function getDisplayField()
	{
		if (isset($this->_displayField)) {
			return $this->_displayField;
		} else {
			return current($this->getTable()->getFieldNames());
		}
	}
	
	/**
	 * @return Ajde_Db_Adapter_Abstract
	 */
	public function getConnection()
	{
		return $this->_connection;
	}
	
	/**
	 * @return Ajde_Db_Table
	 */
	public function getTable()
	{
		return $this->_table;
	}
	
	public function populate($array)
	{
		// TODO: parse array and typecast to match fields settings
		$this->_data = array_merge($this->_data, $array);
	}
	
	public function getValues() {
		$return = array();
		foreach($this->_data as $k => $v) {
			if ($v instanceof Ajde_Model) {
				$return[$k] = $v->getValues();
			} else {
				$return[$k] = $v;
			}
		}
		return $return;
	}
	
	// Load model values	
	public function loadByPK($value)
	{
		$pk = $this->getTable()->getPK();
		return $this->loadByFields(array($pk => $value));
	}
	
	public function loadByField($field, $value)
	{
		return $this->loadByFields(array($field => $value));
	}
	
	public function loadByFields($array)
	{
		$sqlWhere = array();
		$values = array();
		foreach($array as $field => $value) {
			$sqlWhere[] = $field . ' = ?';
			$values[] = $value;
		} 
		$sql = 'SELECT * FROM '.$this->_table.' WHERE ' . implode(' AND ', $sqlWhere) . ' LIMIT 1';
		return $this->_load($sql, $values);
	}
	
	protected function _load($sql, $values)
	{
		$statement = $this->getConnection()->prepare($sql);
		$statement->execute($values);
		$result = $statement->fetch(PDO::FETCH_ASSOC);	
		if ($result === false || empty($result)) {
			return false;
		} else {
			$this->reset();
			$this->populate($result);
			if ($this->_autoloadParents === true) {
				$this->loadParents();
			}
			return true;
		}
	}
		
	/**
	 *
	 * @return boolean 
	 */
	public function save()
	{
		if (method_exists($this, 'beforeSave')) {
			$this->beforeSave();
		}
		$pk = $this->getTable()->getPK();

		$sqlSet = array();
		$values = array();
		
		foreach($this->getTable()->getFieldNames() as $field) {
			// Don't save a field is it's empty or not set
			if ($this->has($field)) {
				if ($this->isEmpty($field) && !$this->getTable()->getFieldProperties($field, 'isRequired')) {
					$sqlSet[] = $field . ' = NULL';
				} elseif(!$this->isEmpty($field)) {
					if ($this->get($field) instanceof Ajde_Db_Function) {
						$sqlSet[] = $field . ' = ' . (string) $this->get($field);
					} else {
						$sqlSet[] = $field . ' = ?';
						$values[] = (string) $this->get($field);
					}
				} elseif ($this->get($field) === 0 || $this->get($field) === '0') {
					$sqlSet[] = $field . ' = ?';
					$values[] = (string) $this->get($field);
				} else {
					// Field is required but has an empty value..
					// (shouldn't have passed validation)
					// TODO: set to empty string or ignore?
				}
			}
		} 
		$values[] = $this->getPK();
		$sql = 'UPDATE ' . $this->_table . ' SET ' . implode(', ', $sqlSet) . ' WHERE ' . $pk . ' = ?';
		$statement = $this->getConnection()->prepare($sql);
		$return = $statement->execute($values);
		if (method_exists($this, 'afterSave')) {
			$this->afterSave();
		}
		return $return;
	}
	
	public function insert($pkValue = null)
	{
		if (method_exists($this, 'beforeInsert')) {
			$this->beforeInsert();
		}
		$pk = $this->getTable()->getPK();
		if (isset($pkValue)) {
			$this->set($pk, $pkValue);
		} else {
			$this->set($pk, null);
		}
		$sqlFields = array();
		$sqlValues = array();
		$values = array();
		foreach($this->getTable()->getFieldNames() as $field) {
			// Don't save a field is it's empty or not set
			if ($this->has($field) && !$this->isEmpty($field)) {
				if ($this->get($field) instanceof Ajde_Db_Function) {
					$sqlFields[] = $field;
					$sqlValues[] = (string) $this->get($field);
				} else {
					$sqlFields[] = $field;
					$sqlValues[] = '?';
					$values[] = (string) $this->get($field);
				}
			} else {
				$this->set($field, null);
			}
		} 
		$sql = 'INSERT INTO ' . $this->_table . ' (' . implode(', ', $sqlFields) . ') VALUES (' . implode(', ', $sqlValues) . ')';
		$statement = $this->getConnection()->prepare($sql);
		$return = $statement->execute($values);
		if (!isset($pkValue)) {
			$this->set($pk, $this->getConnection()->lastInsertId());
		}
		if (method_exists($this, 'afterInsert')) {
			$this->afterInsert();
		}
		return $return;
	}
	
	public function delete()
	{
		$id = $this->getPK();
		$pk = $this->getTable()->getPK();
		$sql = 'DELETE FROM '.$this->_table.' WHERE '.$pk.' = ? LIMIT 1';
		$statement = $this->getConnection()->prepare($sql);
		return $statement->execute(array($id));
	}
	
	public function hasParent($parent)
	{
		if (!$parent instanceof Ajde_Db_Table) {
			// throws error if no table can be found
			$parent = new Ajde_Db_Table($parent);
		}
		$fk = $this->getTable()->getFK($fieldName);
		return $fk;
	}
	
	public function getParents()
	{
		return $this->getTable()->getParents();
	}
	
	public function hasLoaded()
	{
		return !empty($this->_data);
	}
	
	public function loadParents()
	{
		foreach($this->getParents() as $parentTableName) {
			$this->loadParent($parentTableName);
		}
	}
	
	public function loadParent($parent)
	{
		if (empty($this->_data)) {
			// TODO:
			throw new Ajde_Exception('Model ' . (string) $this->getTable() . ' not loaded when loading parent');
		}
		if ($parent instanceof Ajde_Model) {
			$parent = $parent->getTable();
		} elseif (!$parent instanceof Ajde_Db_Table) {
			$parent = new Ajde_Db_Table($parent);
		}
		$fk = $this->getTable()->getFK($parent);
		if (!$this->has($fk['field'])) {
			// No value for FK field
			return false;
		}
		$parentModelName = ucfirst((string) $parent) . 'Model';
		$parentModel = new $parentModelName();
		if ($parentModel->getTable()->getPK() != $fk['parent_field']) {
			// TODO:
			throw new Ajde_Exception('Constraints on non primary key fields are currently not supported');
		}
		$parentModel->loadByPK($this->get($fk['field']));
		$this->set((string) $parent, $parentModel);
	}
	
	public function getAutoloadParents() 
	{
		return $this->_autoloadParents;
	}
	
	public function addValidator($fieldName, Ajde_Model_ValidatorAbstract $validator)
	{
		$validator->setModel($this);
		if (!isset($this->_validators[$fieldName])) {
			$this->_validators[$fieldName] = array();
		}
		$this->_validators[$fieldName][] = $validator;
	}
	
	public function getValidators()
	{
		return $this->_validators;
	}
	
	public function getValidatorsFor($fieldName)
	{
		if (!isset($this->_validators[$fieldname])) {
			$this->_validators[$fieldName] = array();
		}
		return $this->_validators[$fieldName];
	}
	
	public function validate($fieldOptions = array())
	{
		if (method_exists($this, 'beforeValidate')) {
			$return = $this->beforeValidate();
			if ($return !== true && $return !== false) {
				// TODO:
				throw new Ajde_Exception(sprintf("beforeValidate() must return either TRUE or FALSE"));
			}
			if ($return = false) {
				return false;
			}
		}	
		
		if (!$this->hasLoaded()) {
			return false;
		}
		$errors		= array();
		$validator	= $this->_getValidator();
		
		$valid = $validator->validate($fieldOptions);
		if (!$valid) {
			$errors = $validator->getErrors();
		}		
		$this->setValidationErrors($errors);
		
		if (method_exists($this, 'afterValidate')) {
			$this->afterValidate();
		}	
		return $valid;
	}
	
	public function getValidationErrors()
	{
		return parent::getValidationErrors();
	}
	
	/**
	 *
	 * @return Ajde_Model_Validator
	 */
	private function _getValidator()
	{
		return new Ajde_Model_Validator($this);
	}
	
	public function hash()
	{
		$str = implode($this->values());
		return md5($str);
	}
}