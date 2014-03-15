<?php

class Ajde_Crud_Options_Edit_Layout_Rows_Row extends Ajde_Crud_Options
{
	private $_columns;
	private $_column = 0;
	
	/**
	 *
	 * @return Ajde_Crud_Options_Edit_Layout
	 */
	public function up($obj = false) {
		$inter = parent::up($this);
		return parent::up($inter);
	}
	
	// =========================================================================
	// Select functions
	// =========================================================================
	
	/**
	 * Adds a column to the row
	 * 
	 * @return Ajde_Crud_Options_Edit_Layout_Rows_Row_Columns_Column
	 */
	public function addColumn() {
		$this->_column++;
		if (!isset($this->_columns)) {
			$this->_columns = $this->_select('columns');
		}				
		return $this->_columns->_select('column', $this->_column);
	}
		
	// =========================================================================
	// Set functions
	// =========================================================================
	
}