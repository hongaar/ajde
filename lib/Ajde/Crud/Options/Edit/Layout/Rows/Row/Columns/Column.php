<?php

class Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column extends Ajde_Crud_Options
{
    private $_blocks;
    private $_block = 0;

    /**
     *
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row
     */
    public function up($obj = false)
    {
        $inter = parent::up($this);

        return parent::up($inter);
    }

    // =========================================================================
    // Select functions
    // =========================================================================

    /**
     * Adds a block to the column
     *
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column_Blocks_Block
     */
    public function addBlock()
    {
        $this->_block++;
        if (!isset($this->_blocks)) {
            $this->_blocks = $this->_select('blocks');
        }

        return $this->_blocks->_select('block', $this->_block);
    }

    // =========================================================================
    // Set functions
    // =========================================================================

    /**
     * Sets the column width in px
     *
     * @param integer $pixels
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column
     * @deprecated Use setSpan instead
     */
    public function setWidth($pixels)
    {
        return $this->_set('width', $pixels);
    }

    /**
     * Sets the column alignment
     *
     * @param enum $align ('left'|'right')
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column
     * @deprecated Use column order instead
     */
    public function setAlign($alignment)
    {
        return $this->_set('align', $alignment);
    }

    /**
     * Sets the column span in columns (12-based)
     *
     * @param integer $columns
     * @return Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column
     */
    public function setSpan($columns)
    {
        return $this->_set('span', $columns);
    }
}
