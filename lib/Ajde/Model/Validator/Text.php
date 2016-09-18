<?php

class Ajde_Model_Validator_Text extends Ajde_Model_ValidatorAbstract
{
    protected function _validate()
    {
        if (!empty($this->_value)) {
            if ($length = $this->getLength()) {
                if (strlen($this->_value) > $length) {
                    return [
                        'valid' => false,
                        'error' => sprintf(
                            trans('Text is too long (max. %s characters)'), $length
                        ),
                    ];
                }
            }
        }
        $strippedHtml = strip_tags($this->_value);
        if ($this->getIsRequired() && empty($strippedHtml) && $this->getDefault() == '') {
            return ['valid' => false, 'error' => trans('Required field')];
        }

        return ['valid' => true];
    }
}
