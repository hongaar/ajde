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
        $values = [];
        if ($this->_value instanceof Ajde_Db_Function) {
            $sql = $this->_field.$this->_comparison.(string) $this->_value;
        } else {
            $sql = $this->_field.$this->_comparison.':'.spl_object_hash($this);
            $values = [spl_object_hash($this) => $this->_value];
        }

        return [
            'where' => [
                'arguments' => [$sql, $this->_operator],
                'values'    => $values,
            ],
        ];
    }
}
