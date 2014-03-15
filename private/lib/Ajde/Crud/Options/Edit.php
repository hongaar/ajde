<?php

class Ajde_Crud_Options_Edit extends Ajde_Crud_Options
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
	 * @return Ajde_Crud_Options_Edit_Layout
	 */
	public function selectLayout()	{ return $this->_select('layout'); }	
	
	// =========================================================================
	// Set functions
	// =========================================================================
	
	/**
	 * Sets which fields to show
	 * 
	 * @param array $fields
	 * @return Ajde_Crud_Options_Edit
	 */
	public function setShow($fields)	{ return $this->_set('show', $fields); }
	
	/**
	 * Sets a custom template
	 * 
	 * @param string $action
	 * @return Ajde_Crud_Options_Edit
	 */
	public function setAction($action)	{ return $this->_set('action', $action); }
}