<?php

class Ajde_Filter_Match extends Ajde_Filter
{	
	protected $_fields;
	protected $_against;
	protected $_operator;
	protected $_table;
	
	public function __construct($fields, $against, $operator = Ajde_Query::OP_AND, $table = null)
	{
		$this->_fields = $fields;
		$this->_against = $against;
		$this->_operator = $operator;
		$this->_table = $table;
	}
	
	public function prepare(Ajde_Db_Table $table = null)
	{
		if (isset($this->_table)) {
			$useTable = (string) $this->_table;
		} else {
			$useTable = (string) $table;
		} 
		$sql = 'MATCH (' . implode(', ', $this->_fields) . ') AGAINST (:' . spl_object_hash($this) . ')';
		return array(
			'where' => array(
				'arguments' => array($sql, $this->_operator),
				'values' => array(spl_object_hash($this) => $this->_against)
			),
			'select' => array(
				'arguments' => array($sql . ' AS relevancy_' . $useTable)
			)
		);
	}
}