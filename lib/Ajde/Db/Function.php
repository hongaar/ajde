<?php

class Ajde_Db_Function
{
    /**
     * @var string
     */
    private $_function = null;

    /**
     * Ajde_Db_Function constructor.
     *
     * @param string $functionName
     */
    public function __construct($functionName)
    {
        $this->_function = $functionName;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->_function;
    }
}
