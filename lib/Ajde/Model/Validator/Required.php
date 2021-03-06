<?php

class Ajde_Model_Validator_Required extends Ajde_Model_ValidatorAbstract
{
    protected function _validate()
    {
        if (empty($this->_value)) {
            if (!$this->getIsAutoIncrement()) {
                return ['valid' => false, 'error' => trans('Required field')];
            }
        }

        return ['valid' => true];
    }
}
