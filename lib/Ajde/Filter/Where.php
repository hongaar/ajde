<?php

class Ajde_Filter_Where extends Ajde_Filter
{	
	protected $_field;
	protected $_comparison;
	protected $_value;
	protected $_operator;
	
	public function __construct($field, $comparison, $value, $operator = Ajde_Query::OP_AND)
	{
		$this->_field = $field;
		$this->_comparison = $comparison;
		$this->_value = $value;
		$this->_operator = $operator;
	}
	
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
			'where' => array(
				'arguments' => array($sql, $this->_operator),
				'values' => $values
			)
		);
	}
}