<?php

class Ajde_Filter_Having extends Ajde_Filter_Where
{
    protected $_field;
    protected $_comparison;
    protected $_value;
    protected $_operator;

    public function prepare(Ajde_Db_Table $table = null)
    {
        $values = [];
        if ($this->_value instanceof Ajde_Db_Function) {
            $sql = $this->_field . $this->_comparison . (string)$this->_value;
        } else {
            $sql    = $this->_field . $this->_comparison . ':' . spl_object_hash($this);
            $values = [spl_object_hash($this) => $this->_value];
        }

        return [
            'having' => [
                'arguments' => [$sql, $this->_operator],
                'values'    => $values
            ]
        ];
    }
}
