<?php

class Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column_Blocks_Block extends Ajde_Crud_Options
{
    /**
     *
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column
     */
    public function up($obj = false)
    {
        $inter = parent::up($this);

        return parent::up($inter);
    }

    // =========================================================================
    // Select functions
    // =========================================================================

    // =========================================================================
    // Set functions
    // =========================================================================

    /**
     * Sets which fields to show
     *
     * @param array $fields
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column_Blocks_Block
     */
    public function setShow($fields)
    {
        return $this->_set('show', $fields);
    }

    /**
     * Sets block title
     *
     * @param string $title
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column_Blocks_Block
     */
    public function setTitle($title)
    {
        return $this->_set('title', $title);
    }

    /**
     * Sets CSS classname(s)
     *
     * @param string $cssClass
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column_Blocks_Block
     */
    public function setClass($cssClass)
    {
        return $this->_set('class', $cssClass);
    }
}
