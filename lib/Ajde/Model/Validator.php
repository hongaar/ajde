<?php

class Ajde_Model_Validator extends Ajde_Object_Standard
{
    /**
     *
     * @var Ajde_Model
     */
    protected $_model  = null;
    protected $_errors = null;

    public function __construct(Ajde_Model $model)
    {
        $this->_model = $model;
    }

    private function _initValidators($fieldOptions)
    {
        foreach ($fieldOptions as $fieldName => $fieldProperties) {
            switch (issetor($fieldProperties['type'])) {
                case Ajde_Db::FIELD_TYPE_DATE:
                    $this->_model->addValidator($fieldName, new Ajde_Model_Validator_Date());
                    break;
                case 'sort':
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

            if (issetor($fieldProperties['isRequired']) === true && issetor($fieldProperties['default']) == '') {
                $this->_model->addValidator($fieldName, new Ajde_Model_Validator_Required());
            }

            if (issetor($fieldProperties['isUnique']) === true) {
                $this->_model->addValidator($fieldName, new Ajde_Model_Validator_Unique());
            }
        }
    }

    public function shouldValidateDynamicField($fieldOptions)
    {
        if (isset($fieldOptions['showOnlyWhen'])) {
            $showOnlyWhens = $fieldOptions['showOnlyWhen'];
            foreach ($showOnlyWhens as $fieldName => $showWhenValues) {
                $value = -1;
                if ($this->_model->has($fieldName)) {
                    $value = strtolower(str_replace(' ', '', $this->_model->get($fieldName)));
                }
                if (in_array($value, $showWhenValues)) {
                    return true;
                }
            }

            return false;
        }

        return true;
    }

    public function validate($options = [])
    {
        $fieldsArray  = $this->_model->getTable()->getFieldProperties();
        $fieldOptions = [];

        // Add all model fields
        foreach ($fieldsArray as $fieldName => $fieldProperties) {
            $fieldOptions[$fieldName] = array_merge($fieldProperties,
                isset($options[$fieldName]) ? $options[$fieldName] : []);
            if (isset($options[$fieldName])) {
                unset($options[$fieldName]);
            }
        }

        // Add all non-model fields
        foreach ($options as $fieldName => $fieldProperties) {
            $fieldOptions[$fieldName] = $fieldProperties;
        }

        $valid  = true;
        $errors = [];
        $this->_initValidators($fieldOptions);

        foreach ($this->_model->getValidators() as $fieldName => $fieldValidators) {
            foreach ($fieldValidators as $fieldValidator) {
                /* @var $fieldValidator Ajde_Model_ValidatorAbstract */
                $value = null;
                if ($this->_model->has($fieldName)) {
                    $value = $this->_model->get($fieldName);
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    } else {
                        $value = (string)$value;
                    }
                }
                // Only validate when dynamic field is shown
                if ($this->shouldValidateDynamicField($fieldOptions[$fieldName])) {
                    $result = $fieldValidator->validate($fieldOptions[$fieldName], $value);
                    if ($result['valid'] === false) {
                        if (!isset($errors[$fieldName])) {
                            $errors[$fieldName] = [];
                        }
                        $errors[$fieldName][] = $result['error'];
                        $valid                = false;
                    }
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
