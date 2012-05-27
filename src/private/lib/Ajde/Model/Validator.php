<?php

class Ajde_Model_Validator extends Ajde_Object_Standard
{	
	/**
	 *
	 * @var Ajde_Model
	 */
	protected $_model = null;
	protected $_errors = null;
	
	public function __construct(Ajde_Model $model)
	{
		$this->_model = $model;
	}
	
	private function _initValidators($fieldOptions)
	{
		foreach($fieldOptions as $fieldName => $fieldProperties) {
			switch ($fieldProperties['type']) {
				case Ajde_Db::FIELD_TYPE_DATE:				
					$this->_model->addValidator($fieldName, new Ajde_Model_Validator_Date());
					break;
				case Ajde_Db::FIELD_TYPE_NUMERIC:			
					$this->_model->addValidator($fieldName, new Ajde_Model_Validator_Numeric());
					break;
				case Ajde_Db::FIELD_TYPE_TEXT:
					$this->_model->addValidator($fieldName, new Ajde_Model_Validator_Text());
					break;
				case Ajde_Db::FIELD_TYPE_ENUM:
					$this->_model->addValidator($fieldName, new Ajde_Model_Validator_Enum());
					break;
				case Ajde_Db::FIELD_TYPE_SPATIAL:
					$this->_model->addValidator($fieldName, new Ajde_Model_Validator_Spatial());
					break;
				default :
					break;
			}
			
			if ($fieldProperties['isRequired'] === true && $fieldProperties['default'] == '') {
				$this->_model->addValidator($fieldName, new Ajde_Model_Validator_Required());
			}
		}
	}
	
	public function validate($options = array())
	{
		$fieldsArray = $this->_model->getTable()->getFieldProperties();
		$fieldOptions = array();
		foreach($fieldsArray as $fieldName => $fieldProperties) {	
			$fieldOptions[$fieldName] = array_merge($fieldProperties, issetor($options[$fieldName], array()));		
		}
		
		$valid = true;
		$errors = array();
		$this->_initValidators($fieldOptions);
		
		foreach($this->_model->getValidators() as $fieldName => $fieldValidators) {
			foreach($fieldValidators as $fieldValidator) {
				/* @var $validator Ajde_Model_ValidatorAbstract */
				$value = null;
				if ($this->_model->has($fieldName)) {
					$value = $this->_model->get($fieldName);
				}
				$result = $fieldValidator->validate($fieldOptions[$fieldName], $value);				
				if ($result['valid'] === false) {
					if (!isset($errors[$fieldName])) {
						$errors[$fieldName] = array();
					}
					$errors[$fieldName][] = $result['error'];
					$valid = false;
				}
			}
		}
		$this->_errors = $errors;
		return $valid;
	}
	
	public function getErrors()
	{
		return $this->_errors;
	}
}