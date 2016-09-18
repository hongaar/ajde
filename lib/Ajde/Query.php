<?php

class Ajde_Query extends Ajde_Object_Standard
{
    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    const OP_AND = 'AND';
    const OP_OR = 'OR';

    const JOIN_INNER = 'INNER';
    const JOIN_LEFT = 'LEFT';

    public $select = [];
    public $distinct = false;
    public $from = [];
    public $where = [];
    public $having = [];
    public $join = [];
    public $groupBy = [];
    public $orderBy = [];
    public $limit = ['start' => null, 'count' => null];

    public function reset()
    {
        $this->select = [];
        $this->from = [];
        $this->where = [];
        $this->having = [];
        $this->join = [];
        $this->groupBy = [];
        $this->orderBy = [];
        $this->limit = ['start' => null, 'count' => null];
    }

    public function addSelect($select)
    {
        $this->select[] = $select;
    }

    public function setDistinct($distinct)
    {
        $this->distinct = (bool) $distinct;
    }

    public function addFrom($from)
    {
        $this->from[] = $from;
    }

    public function addWhere($where, $operator = self::OP_AND)
    {
        $this->where[] = ['sql' => $where, 'operator' => $operator];
    }

    public function addHaving($having, $operator = self::OP_AND)
    {
        $this->having[] = ['sql' => $having, 'operator' => $operator];
    }

    public function addJoin($join, $type = self::JOIN_INNER)
    {
        $this->join[] = ['sql' => $join, 'type' => $type];
    }

    public function addOrderBy($field, $direction = self::ORDER_ASC)
    {
        $direction = strtoupper($direction);
        if (!in_array($direction, [self::ORDER_ASC, self::ORDER_DESC])) {
            // TODO:
            throw new Ajde_Exception('Collection ordering direction "'.$direction.'" not valid');
        }
        $this->orderBy[] = ['field' => $field, 'direction' => $direction];
    }

    public function addGroupBy($field)
    {
        $this->groupBy[] = $field;
    }

    public function limit($count, $start = 0)
    {
        $this->limit = ['count' => (int) $count, 'start' => (int) $start];
    }

    public function getSql()
    {
        $sql = '';
        $distinct = $this->distinct ? 'DISTINCT ' : '';

        // SELECT
        if (empty($this->select)) {
            $sql .= 'SELECT '.$distinct.'*';
        } else {
            $sql .= 'SELECT '.$distinct.implode(', ', $this->select);
        }

        // FROM
        if (empty($this->from)) {
            // TODO:
            throw new Ajde_Exception('FROM clause can not be empty in query');
        } else {
            $sql .= ' FROM '.implode(', ', $this->from);
        }

        // JOIN
        if (!empty($this->join)) {
            foreach ($this->join as $join) {
                $sql .= ' '.$join['type'].' JOIN '.$join['sql'];
            }
        }

        // WHERE
        if (!empty($this->where)) {
            $first = true;
            $sql .= ' WHERE';
            foreach ($this->where as $where) {
                if ($first === false) {
                    $sql .= ' '.$where['operator'];
                }
                $sql .= ' '.$where['sql'];
                $first = false;
            }
        }

        // GROUP BY
        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY';
            $sql .= ' '.implode(', ', $this->groupBy);
        }

        // HAVING
        if (!empty($this->having)) {
            $first = true;
            $sql .= ' HAVING';
            foreach ($this->having as $having) {
                if ($first === false) {
                    $sql .= ' '.$having['operator'];
                }
                $sql .= ' '.$having['sql'];
                $first = false;
            }
        }

        // ORDER BY
        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY';
            $orderBySql = [];
            foreach ($this->orderBy as $orderBy) {
                $orderBySql[] = $orderBy['field'].' '.$orderBy['direction'];
            }
            $sql .= ' '.implode(', ', $orderBySql);
        }

        // LIMIT
        if (isset($this->limit['count']) && !isset($this->limit['start'])) {
            $sql .= ' LIMIT '.$this->limit['count'];
        } elseif (isset($this->limit['count']) && isset($this->limit['start'])) {
            $sql .= ' LIMIT '.$this->limit['start'].', '.$this->limit['count'];
        }

        return $sql;
    }
}
