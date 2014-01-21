<?php

class Ajde_Model_Validator_HasChildren extends Ajde_Model_ValidatorAbstract
{
    protected $_refTable;
    protected $_fk;
    protected $_niceName;

    public function setReferenceOptions($table, $fk, $niceName = 'reference')
    {
        $this->_refTable = $table;
        $this->_fk = $fk;
        $this->_niceName = $niceName;
    }

	protected function _validate()
	{
        $pk = $this->getModel()->getPK();
        $model = $this->getModel();

        if (empty($pk)) {
            // model not saved yet, we cannot validate now, return true
            return array('valid' => true);
        }

        $sql = "SELECT COUNT(*) FROM " . $this->_refTable . " WHERE " . $this->_fk . " = " . $pk;
        $statement = $model->getConnection()->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_COLUMN);

        $count = $results[0];

        if ($count == 0) {
            return array('valid' => false, 'error' => __('Please add at least one ' . $this->_niceName));
        }
        return array('valid' => true);
	}
}