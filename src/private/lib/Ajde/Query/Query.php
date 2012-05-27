<?php

class Ajde_Query extends Ajde_Object_Standard
{
	const ORDER_ASC 	= 'ASC';
	const ORDER_DESC 	= 'DESC';
	
	const OP_AND		= 'AND';
	const OP_OR			= 'OR';
	
	const JOIN_INNER	= 'INNER';
	const JOIN_LEFT		= 'LEFT';
	
	public $select = array();
	public $from = array();
	public $where = array();
	public $join = array();	
	public $groupBy = array();
	public $orderBy = array();
	public $limit = array('start' => null, 'count' => null);
	
	public function reset()
	{
		$this->select = array();
		$this->from = array();
		$this->where = array();
		$this->join = array();	
		$this->groupBy = array();
		$this->orderBy = array();
		$this->limit = array('start' => null, 'count' => null);	
	}
	
	public function addSelect($select)
	{
		$this->select[] = $select;		
	}
	
	public function addFrom($from)
	{
		$this->from[] = $from;
	}
	
	public function addWhere($where, $operator = self::OP_AND)
	{
		$this->where[] = array('sql' => $where, 'operator' => $operator);
	}
		
	public function addJoin($join, $type = self::JOIN_INNER)
	{
		$this->join[] = array('sql' => $join, 'type' => $type);
	}
		
	public function addOrderBy($field, $direction = self::ORDER_ASC)
	{
		$direction = strtoupper($direction);
		if (!in_array($direction, array(self::ORDER_ASC, self::ORDER_DESC))) {
			// TODO: 
			throw new Ajde_Exception('Collection ordering direction "'.$direction.'" not valid');
		}
		$this->orderBy[] = array('field' => $field, 'direction' => $direction);
	}
	
	public function addGroupBy($field)
	{
		$this->groupBy[] = $field;
	}
	
	public function limit($count, $start = 0)
	{
		$this->limit = array('count' => (int) $count, 'start' => (int) $start);
	}
	
	public function getSql()
	{
		$sql = '';
		
		// SELECT
		if (empty($this->select)) {
			$sql .= 'SELECT *';
		} else {
			$sql .= 'SELECT ' . implode(', ', $this->select);
		}
		
		// FROM
		if (empty($this->from)) {
			// TODO:
			throw new Ajde_Exception('FROM clause can not be empty in query');
		} else {
			$sql .= ' FROM ' . implode(', ', $this->from);
		}
		
		// JOIN
		if (!empty($this->join)) {
			foreach($this->join as $join) {
				$sql .= ' ' . $join['type'] . ' JOIN ' . $join['sql'];
			}
		}
		
		// WHERE
		if (!empty($this->where)) {
			$first = true;
			$sql .= ' WHERE';
			foreach($this->where as $where) {
				if ($first === false) {
					$sql .= ' ' . $where['operator'];
				}
				$sql .= ' ' . $where['sql'];
				$first = false;
			}
		}
		
		// GROUP BY
		if (!empty($this->groupBy)) {
			$sql .= ' GROUP BY';			
			$sql .= ' ' . implode(', ', $this->groupBy);
		}
		
		// ORDER BY
		if (!empty($this->orderBy)) {
			$sql .= ' ORDER BY';
			$orderBySql = array();
			foreach($this->orderBy as $orderBy) {
				$orderBySql[] = $orderBy['field'] . ' ' . $orderBy['direction'];
			}
			$sql .= ' ' . implode(', ', $orderBySql);
		}
		
		// LIMIT
		if (isset($this->limit['count']) && !isset($this->limit['start'])) {
			$sql 	.= ' LIMIT '.$this->limit['count'];
		} elseif (isset($this->limit['count']) && isset($this->limit['start'])) {
			$sql 	.= ' LIMIT '.$this->limit['start'].', '.$this->limit['count'];	
		}		
		
		return $sql;
	}
}
