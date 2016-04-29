<?php

class Ajde_Model_Validator_Numeric extends Ajde_Model_ValidatorAbstract
{
    protected function _validate()
    {
        if (!empty($this->_value)) {
            if (!is_numeric($this->_value)) {
                return ['valid' => false, 'error' => trans('Not a number')];
            }
        }

        return ['valid' => true];
    }
}
