<?php

class Ajde_Model_Validator_Numeric extends Ajde_Model_ValidatorAbstract
{
	protected function _validate()
	{
        if (!empty($this->_value)) {            
			if (!is_numeric($this->_value)) {
                return array( 'valid' => false, 'error' => __('Not a number') );
			}
		}
		return array('valid' => true);
	}
}