<?php

class Ajde_Model_Validator_Unique extends Ajde_Model_ValidatorAbstract
{
    protected function _validate()
    {
        if (!empty($this->_value)) {

            //			return array('valid' => false, 'error' => __('This ' . $this->getValue() . ' already exists'));

            $pkField = $this->getModel()->getTable()->getPK();
            $pkValue = $this->getModel()->getPK();

            if ($this->getModel()->isFieldEncrypted($this->getName())) {
                $testValue = $this->getModel()->doEncrypt($this->getValue());
            } else {
                $testValue = $this->getValue();
            }

            if ($pkValue) {
                // Existing
                $sql = 'SELECT * FROM '.$this->getModel()->getTable().' WHERE '.$this->getName().' = ? AND '.$pkField.' != ? LIMIT 1';
                $values = [$testValue, $pkValue];
            } else {
                // New record
                $sql = 'SELECT * FROM '.$this->getModel()->getTable().' WHERE '.$this->getName().' = ? LIMIT 1';
                $values = [$testValue];
            }

            $connection = Ajde_Db::getInstance()->getConnection();
            $statement = $connection->prepare($sql);
            $statement->execute($values);
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result === false || empty($result)) {
                // Not found
            } else {
                return ['valid' => false, 'error' => trans('This '.$this->getName().' already exists')];
            }
        }

        return ['valid' => true];
    }
}
