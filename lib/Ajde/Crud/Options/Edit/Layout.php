<?php

class Ajde_Crud_Options_Edit_Layout extends Ajde_Crud_Options
{
    private $_rows;
    private $_row = 0;

    /**
     * @return Ajde_Crud_Options_Edit
     */
    public function up($obj = false)
    {
        return parent::up($this);
    }

    // =========================================================================
    // Select functions
    // =========================================================================

    /**
     * Adds a row to the layout.
     *
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row
     */
    public function addRow()
    {
        $this->_row++;
        if (!isset($this->_rows)) {
            $this->_rows = $this->_select('rows');
        }

        return $this->_rows->_select('row', $this->_row);
    }

    // =========================================================================
    // Set functions
    // =========================================================================
}
