<?php

class Ajde_Crud_Options_List extends Ajde_Crud_Options
{	
	/**
	 *
	 * @return Ajde_Crud_Options
	 */
	public function up($obj = false) {
		return parent::up($this);
	}
	
	// =========================================================================
	// Select functions
	// =========================================================================
	
	/**
	 *
	 * @return Ajde_Crud_Options_List_Buttons
	 */
	public function selectButtons()	{ return $this->_select('buttons'); }
	
	/**
	 *
	 * @return Ajde_Crud_Options_List_View
	 */
	public function selectView()	{ return $this->_select('view'); }	
	
	// =========================================================================
	// Set functions
	// =========================================================================
	
	/**
	 * Set fieldname to set as main column in listview
	 * 
	 * @param string $main
	 * @return Ajde_Crud_Options_List
	 */
	public function setMain($fieldname)	{ return $this->_set('main', $fieldname); }	
	
	/**
	 * Sets which fields to show
	 * 
	 * @param array $fields
	 * @return Ajde_Crud_Options_List
	 */
	public function setShow($fields)	{ return $this->_set('show', $fields); }
	
	/**
	 * Sets which fields to show in grid view
	 * 
	 * @param array $fields
	 * @return Ajde_Crud_Options_List
	 */
	public function setGridShow($fields) { return $this->_set('gridShow', $fields); }
	
	/**
	 * Sets thumbnail dimensions of images
	 * 
	 * @param type $width
	 * @param type $height
	 * @return Ajde_Crud_Options_List 
	 */
	public function setThumbDim($width, $height) { return $this->_set('thumbDim', array('width' => $width, 'height' => $height)); }
}