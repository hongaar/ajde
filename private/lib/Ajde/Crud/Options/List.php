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
	 * Show the search box
	 * 
	 * @param boolean $show
	 * @return Ajde_Crud_Options_List
	 */
	public function setSearch($show)	{ return $this->_set('search', $show); }
	
	/**
	 * Shows the column names
	 * 
	 * @param boolean $show
	 * @return Ajde_Crud_Options_List
	 */
	public function setShowColumnNames($show)	{ return $this->_set('showColumnNames', $show); }	
	
	/**
	 * Shows the table header
	 * 
	 * @param boolean $show
	 * @return Ajde_Crud_Options_List
	 */
	public function setShowHeader($show)	{ return $this->_set('showHeader', $show); }

    /**
     * Shows the table toolbar
     *
     * @param boolean $show
     * @return Ajde_Crud_Options_List
     */
    public function setShowToolbar($show)	{ return $this->_set('showToolbar', $show); }
	
	/**
	 * Shows the table header
	 * 
	 * @param boolean $show
	 * @return Ajde_Crud_Options_List
	 */
	public function setShowFooter($show)	{ return $this->_set('showFooter', $show); }
	
	/**
	 * Single click on table row goes into edit mode
	 * 
	 * @param boolean $singleClick
	 * @return Ajde_Crud_Options_List
	 */
	public function setSingleClickEdits($singleClick)	{ return $this->_set('singleClickEdits', $singleClick); }
	
	/**
	 * Sets which fields to show
	 * 
	 * @param array $fields
	 * @return Ajde_Crud_Options_List
	 */
	public function setShow($fields)	{ return $this->_set('show', $fields); }
	
	/**
	 * Sets a function which generates the row class
	 * 
	 * @param array $fields
	 * @return Ajde_Crud_Options_List
	 */
	public function setRowClassFunction($modelFunction)	{ return $this->_set('rowClassFunction', $modelFunction); }
	
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
	
	/**
	 * Sets info panel function
	 * 
	 * @param string $function
	 * @return Ajde_Crud_Options_List 
	 */
	public function setPanelFunction($function) { return $this->_set('panelFunction', $function); }
}