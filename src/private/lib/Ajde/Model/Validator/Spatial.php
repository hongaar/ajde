<?php

class Ajde_Model_Validator_Spatial extends Ajde_Model_ValidatorAbstract
{
	protected function _validate()
	{
        $trimmed = trim($this->_value);
        if ($this->getIsRequired() && empty($trimmed)) {
			return array('valid' => false, 'error' => __('Required field'));
		}
		return array('valid' => true);
	}
}