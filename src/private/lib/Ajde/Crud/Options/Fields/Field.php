<?php

class Ajde_Crud_Options_Fields_Field extends Ajde_Crud_Options
{		
	/**
	 *
	 * @return Ajde_Crud_Options_Fields
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
	 * Set fieldtype for field (see Ajde_Crud_Field_*)
	 * 
	 * @param string $type
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setType($type) { return $this->_set('type', $type); }
	
	/**
	 * For 'file' fieldtypes, sets the dir to save the files relative to the sites root
	 * 
	 * @param string $saveDir
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setSaveDir($saveDir) { return $this->_set('saveDir', $saveDir); }
	
	/**
	 * For 'file' fieldtypes, sets the allowed extensions of the uploaded files
	 * 
	 * @param array $extensions
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setExtensions($extensions) { return $this->_set('extensions', $extensions); }
	
	/**
	 * For 'file' fieldtypes, can multiple files be uploaded at once?
	 * 
	 * @param boolean $multiple
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setMultiple($multiple) { return $this->_set('multiple', $multiple); }
	
	/**
	 * Overrides the field label
	 * 
	 * @param string $label
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setLabel($label) { return $this->_set('label', $label); }
	
	/**
	 * Overrides the field length
	 * 
	 * @param integer $length
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setLength($length) { return $this->_set('length', $length); }
	
	/**
	 * Overrides whether the field is required
	 * 
	 * @param boolean $isRequired
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setIsRequired($isRequired) { return $this->_set('isRequired', $isRequired); }
	
	/**
	 * Overrides the default value
	 * 
	 * @param mixed $default
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setDefault($default) { return $this->_set('default', $default); }
	
	/**
	 * Overrides whether the field has the auto increment attribute
	 * 
	 * @param boolean $isAutoIncrement
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setIsAutoIncrement($isAutoIncrement) { return $this->_set('isAutoIncrement', $isAutoIncrement); }
	
	/**
	 * Sets the helptext
	 * 
	 * @param string help
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setHelp($help) { return $this->_set('help', $help); }
	
	/**
	 * Whether the field is read only
	 * 
	 * @param boolean $isReadonly
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setIsReadonly($isReadonly) { return $this->_set('readonly', $isReadonly); }
	
	/**
	 * Sets the field value
	 * 
	 * @param string $value
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setValue($value) { return $this->_set('value', $value); }
	
	/**
	 * Sets the allowed values
	 * 
	 * @param array $filter
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setFilter($filter) { return $this->_set('filter', $filter); }
	
	/**
	 * Sets an array of Ajde_Filter to apply to field
	 * 
	 * @param array $advancedFilter
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setAdvancedFilter($advancedFilter) { return $this->_set('advancedFilter', $advancedFilter); }
	
	/**
	 * Sets the display function of the model for the list view
	 * 
	 * @param string $function
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setFunction($function) { return $this->_set('function', $function); }
				
	/**
	 * Sets thumbnail dimensions of images
	 * 
	 * @param type $width
	 * @param type $height
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setThumbDim($width, $height) { return $this->_set('thumbDim', array('width' => $width, 'height' => $height)); }
	
	/**
	 * Put emphasis on this field
	 * 
	 * @param boolean $emphasis
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setEmphasis($emphasis) { return $this->_set('emphasis', $emphasis); }
	
	/**
	 * Sets placeholder for this field
	 * 
	 * @param string $emphasis
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setPlaceholder($placeholder) { return $this->_set('placeholder', $placeholder); }
	
	/**
	 * Defines a many-to-many relationshop for fields with type 'multiple'
	 * 
	 * @param string $table
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setCrossReferenceTable($table) { return $this->_set('crossReferenceTable', $table); }	
	
	/**
	 * Display the label?
	 * 
	 * @param boolean $display
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setShowLabel($display) { return $this->_set('showLabel', $display); }	
	
	/**
	 * Sets the edit route for fields with type 'multiple'
	 * 
	 * @param string $route
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setEditRoute($route) { return $this->_set('editRoute', $route); }
	
	/**
	 * Disables rich text editing for text fields
	 * 
	 * @param boolean $disable
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setDisableRichText($disable) { return $this->_set('disableRichText', $disable); }
	
	/**
	 * Sets the textarea height in em for text fields
	 * 
	 * @param integer $em
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setTextInputHeight($em) { return $this->_set('textInputHeight', $em); }
	
	/**
	 * Sets the textarea width in em for text fields
	 * 
	 * @param integer $em
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setTextInputWidth($em) { return $this->_set('textInputWidth', $em); }
	
	/**
	 * Sets the model to use for fields with type 'multiple'
	 * 
	 * @param string $model
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setModelName($model) { return $this->_set('modelName', $model); }
    
    /**
	 * Use a simple selector for fields with type 'multiple'
	 * 
	 * @param boolean $simple
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setSimpleSelector($simple) { return $this->_set('simpleSelector', $simple); }
	
	/**
	 * Adds a column to the cross reference table (for fields with type 'multiple')
	 * 
	 * @param string $field
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function addTableField($field) {
		$fields = ($this->has('tableFields') ? $this->get('tableFields') : array());
		$fields[] = array('name' => $field, 'type' => 'text');
		return $this->_set('tableFields', $fields);
	}
	
	/**
	 * Adds an image column to the cross reference table (for fields with type 'multiple')
	 * 
	 * @param string $field
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function addTableFileField($field, $saveDir) {
		$fields = ($this->has('tableFields') ? $this->get('tableFields') : array());
		$fields[] = array('name' => $field, 'type' => 'file', 'saveDir' => $saveDir);
		return $this->_set('tableFields', $fields);
	}
    
    /**
	 * Use an image for spatial field instead of Google Maps
	 * 
	 * @param boolean $image
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
    public function setUseImage($image) {
        return $this->_set('useImage', $image);
    }
}