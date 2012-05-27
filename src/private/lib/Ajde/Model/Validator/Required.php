<?php

class Ajde_Model_Validator_Required extends Ajde_Model_ValidatorAbstract
{
	protected function _validate()
	{
		if (empty($this->_value)) {
			if (!$this->getIsAutoIncrement()) {
				return array('valid' => false, 'error' => __('Required field'));
			}
		}
		return array('valid' => true);
	}
}