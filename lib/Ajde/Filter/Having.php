<?php

class Ajde_Filter_Having extends Ajde_Filter_Where
{	
	protected $_field;
	protected $_comparison;
	protected $_value;
	protected $_operator;
	
	public function prepare(Ajde_Db_Table $table = null)
	{
		$values = array();
		if ($this->_value instanceof Ajde_Db_Function) {
			$sql = $this->_field . $this->_comparison . (string) $this->_value;
		} else {
			$sql = $this->_field . $this->_comparison . ':' . spl_object_hash($this);
			$values = array(spl_object_hash($this) => $this->_value);
		}
		return array(
			'having' => array(
				'arguments' => array($sql, $this->_operator),
				'values' => $values
			)
		);
	}
}