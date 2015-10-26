<?php

class Ajde_Crud_Options_Fields extends Ajde_Crud_Options
{
    /**
     *
     * @return Ajde_Crud_Options
     */
    public function up($obj = false)
    {
        return parent::up($this);
    }

    // =========================================================================
    // Select functions
    // =========================================================================

    /**
     *
     * @return Ajde_Crud_Options_Fields_Field
     */
    public function selectField($name)
    {
        return $this->_select('field', $name);
    }

    // =========================================================================
    // Set functions
    // =========================================================================

}
