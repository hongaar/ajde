<?php

class Ajde_Crud extends Ajde_Object_Standard
{
	protected $_model = null;
	protected $_collection = null;
	
	protected $_fields = null;
	
	//protected $_operation = null;
	protected $_operation = 'list';

	public function __construct($model, $options = array()) {
		if ($model instanceof Ajde_Model) {
			$this->_model = $model;
		} else {
			$modelName = $this->toCamelCase($model, true) . 'Model';
			$this->_model = new $modelName();
		}
		$this->setOptions($options);
	}
	
	public function __toString()
	{
		try {
			$output = $this->output();
		} catch (Exception $e) {
			$output = Ajde_Exception_Handler::handler($e);
		}
		return $output;
	}
	
	public function output()
	{
		$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/crud:' . $this->getOperation()));
		$controller->setCrudInstance($this);
		return $controller->invoke();
	}
	
	/**
	 * GETTERS & SETTERS 
	 */
	
	/**
	 *
	 * @return string
	 */
	public function getAction()
	{
		if (!$this->hasAction()) {
			$this->setAction('list');
		}
		return parent::getAction();
	}
	
	public function setAction($value)
	{
		if (substr($value, 0, 4) === 'edit' || substr($value, 0, 4) === 'list') {
			$this->setOperation(substr($value, 0, 4));
		} 
		parent::setAction($value);
	}
	
	public function setOperation($operation)
	{
		$this->_operation = $operation;
	}
	
	public function getOperation()
	{
//		if (!isset($this->_operation)) {
//			if (Ajde::app()->getRequest()->has('new')) {
//				$this->setOperation('new'); 
//			} else if (Ajde::app()->getRequest()->has('edit')) {
//				$this->setOperation('edit'); 
//			} else {
//				$this->setOperation('list'); 
//			}
//		}
		return $this->_operation;
	}
	
	/**
	 * OPTIONS
	 */
	
	public function getOption($name, $default = false)
	{
		$path = explode('.', $name);
		$options = $this->getOptions();
		foreach($path as $key) {
			if (isset($options[$key])) {
				$options = $options[$key];
			} else {
				return $default;
			}
		}
		return $options;
	}
	
	public function setOption($name, $value)
	{
		$path = explode('.', $name);
		$options = $this->getOptions();
		$wc = &$options;
		foreach($path as $key) {
			if (!isset($wc[$key])) {
				$wc[$key] = array();
			}
			$wc = &$wc[$key];
		}
		$wc = $value;
		$this->setOptions($options);
	}
		
	/**
	 *
	 * @return array
	 */
	public function getOptions($key = null)
	{
		if (isset($key)) {
			$options = parent::getOptions();	
			return issetor($options[$key], array());
		} else {
			return parent::getOptions();
		}
	}
	
	public function setOptions($value)
	{
		parent::setOptions($value);
	}
	
	/**
	 * MISC 
	 */
	
	public function setItem($value)
	{
		parent::setItem($value);
	}
	
	public function setCustomTemplateModule($value)
	{
		parent::setCustomTemplateModule($value);
	}
	
	public function getCustomTemplateModule()
	{
		if (parent::hasCustomTemplateModule()) {
			return parent::getCustomTemplateModule();
		}
		return (string) $this->getModel()->getTable();		
	}
	
	/**
	 * @return Ajde_Collection
	 */
	public function getCollection()
	{
		if (!isset($this->_collection))	{
			$collectionName = str_replace('Model', '', get_class($this->getModel())) . 'Collection';
			$this->_collection = new $collectionName();
		}
		return $this->_collection;
	}
	
	/**
	 * @return Ajde_Model
	 */
	public function getModel()
	{
		return $this->_model;
	}
	
	/**
	 *
	 * @return string
	 */
	public function getHash()
	{
		return spl_object_hash($this);
	}	
	
	/**
	 * HELPERS
	 */
	
	public function loadItem($id = null)
	{
		$model = $this->getModel();
		if (isset($id)) {
			$model->loadByPK($id);
		}
		$this->setItem($model);
		return $this->getItem();
	}

	/**
	 *
	 * @return Ajde_Model 
	 */
	public function getItem()
	{
		if ($this->isNew()) {
			return $this->getModel();
		}
		if (!$this->getModel()->getPK()) {
			$model = $this->getModel();
			if (!$model->loadByPK($this->getId())) {
				Ajde_Http_Response::redirectNotFound();
			} else {
				if (!$model->getAutoloadParents()) {
					$model->loadParents();
				}
			}
		}
		return $this->getModel();
	}
	
	public function isNew()
	{
		return (!$this->hasId() || $this->getId() === false || is_null($this->getId()));
	}
	
	/**
	 *
	 * @return Ajde_Collection
	 */
	public function getItems()
	{
		$collection = $this->getCollection();
		
		// Collection view
		if ($collection->hasView()) {
			$collection->applyView();
		}
		
		$collection->load();
		$collection->loadParents();
		return $collection;
	}
	
	public function getFields()
	{
		if (!isset($this->_fields)) {
			$model = $this->getModel();
			$item = $this->getItem();
			$fields = array();

			$fieldsArray = $model->getTable()->getFieldProperties();		
			$parents = $item->getTable()->getParents();

			foreach($fieldsArray as $fieldProperties) {
				$fieldOptions = $this->getFieldOptions($fieldProperties['name'], $fieldProperties);
				
				if (in_array($fieldOptions['name'], $parents)) {
					$fieldOptions['type'] = 'fk';
				}
				
				$fieldClass = Ajde_Core_ExternalLibs::getClassname("Ajde_Crud_Field_" . ucfirst($fieldOptions['type']));				
				$field = new $fieldClass($this, $fieldOptions);
							
				if ($this->getOperation() === 'edit') {
					if (!$field->hasValue() || $field->hasEmpty('value')) {
						if ($this->isNew() && $field->hasNotEmpty('default')) {
							$field->setValue($field->getDefault());
						} elseif (!$this->isNew()) {							
							$field->setValue($item->get($field->getName()));
						} else {
							$field->setValue(false);
						}
					}
				}
				
				$fields[$field->getName()] = $field;
			}
			$this->_fields = $fields;
		}
		return $this->_fields;
	}
	
	/**
	 *
	 * @param string $fieldName
	 * @return Ajde_Crud_Field
	 * @throws Ajde_Exception 
	 */
	public function getField($fieldName)
	{
		if (!isset($this->_fields)) {
			$this->getFields();
		}
		if (isset($this->_fields[$fieldName])) {
			return $this->_fields[$fieldName];
		} else {
			// TODO:
			throw new Ajde_Exception($fieldName . ' is not a field in ' . (string) $this->getModel()->getTable());
		}
	}
	
	public function getFieldOptions($fieldName, $fieldProperties = array())
	{
		$fieldsOptions = $this->getOptions('fields');
		$fieldOptions = issetor($fieldsOptions[$fieldName], array());
		return array_merge($fieldProperties, $fieldOptions);
	}
	
	public function getFieldLabels()
	{
		$model = $this->getModel();
		return $model->getTable()->getFieldLabels();
	}
	
	public function setSessionName($name)
	{
		parent::setSessionName($name);
	}
	
	public function getSessionName()
	{
		if (parent::hasSessionName()) {
			return parent::getSessionName();
		} else {
			return (string) $this->getModel()->getTable();
		}
	}
		
	/**
	 * RENDERING
	 */
	
	public function getTemplate()
	{
		$defaultTemplate = new Ajde_Template(MODULE_DIR . '_core/', 'crud/' . $this->getOperation());
		Ajde::app()->getDocument()->autoAddResources($defaultTemplate);
		if ($this->_hasCustomTemplate()) {			
			$base = $this->_getCustomTemplateBase();
			$action = $this->_getCustomTemplateAction();
			return new Ajde_Template($base, $action);
		}
		return $defaultTemplate;
	}
		
	private function _hasCustomTemplate()
	{
		$base = $this->_getCustomTemplateBase();
		$action = $this->_getCustomTemplateAction();
		return Ajde_Template::exist($base, $action) !== false;
	}
	
	private function _getCustomTemplateBase()
	{
		return MODULE_DIR . $this->getCustomTemplateModule() . '/';
	}
	
	private function _getCustomTemplateAction()
	{
		return 'crud/' . (string) $this->getModel()->getTable() . '/' . $this->getAction();
	}
}