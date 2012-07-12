<?php

class Ajde_Crud_Options_List_Buttons extends Ajde_Crud_Options
{	
	/**
	 *
	 * @return Ajde_Crud_Options_List 
	 */
	public function up($obj = false) {
		return parent::up($this);
	}
	
	// =========================================================================
	// Select functions
	// =========================================================================
			
	// =========================================================================
	// Set functions
	// =========================================================================
	
	/**
	 * Show the delete button
	 * 
	 * @param boolean $show
	 * @return Ajde_Crud_Options_List_Buttons 
	 */
	public function setDelete($show) { return $this->_set('delete', $show); }
	
	/**
	 * Show the new button
	 * 
	 * @param boolean $show
	 * @return Ajde_Crud_Options_List_Buttons 
	 */
	public function setNew($show) { return $this->_set('new', $show); }
	
	/**
	 * Show the edit button
	 * 
	 * @param boolean $show
	 * @return Ajde_Crud_Options_List_Buttons 
	 */
	public function setEdit($show) { return $this->_set('edit', $show); }
	
	/**
	 * Adds a custom button
	 * 
	 * @param name $name Identifier of the button
	 * @param text $text Text to display
	 * @param type $class Optional classname to add
	 * @return Ajde_Crud_Options_List_Buttons 
	 */
	public function setCustom($name, $text, $class = null) { return $this->_set($name, array('text' => $text, 'class' => $class)); }
}