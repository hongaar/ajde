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
     * Filter based on language
     *
     * @param mixed $mode On of false|'parent'|'page'
     * @return Ajde_Crud_Options_Fields_Field
     */
    public function setFilterLang($mode) { return $this->_set('filterLang', $mode); }
	
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
	 * Sets the order field of the foreign table
	 * 
	 * @param string $field
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setOrderBy($field) { return $this->_set('orderBy', $field); }
	
	/**
	 * Sets the display function of the model for the list view
	 * 
	 * @param string $function
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setFunction($function) { return $this->_set('function', $function); }
	
	/**
	 * Sets the display function arguments of the model for the list view
	 *
	 * @param array $arguments
	 * @return Ajde_Crud_Options_Fields_Field
	 */
	public function setFunctionArgs($args) { return $this->_set('functionArgs', $args); }
				
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
	 * Sets the edit route function for fields with type 'multiple'
	 * 
	 * @param string $function
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setEditRouteFunction($function) { return $this->_set('editRouteFunction', $function); }
	
	/**
	 * Sets the list route for fields with type 'fk'
	 * 
	 * @param string $route
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setListRoute($route) { return $this->_set('listRoute', $route); }
	
	/**
	 * Sets the list route function for fields with type 'fk'
	 *
	 * @param string $function
	 * @return Ajde_Crud_Options_Fields_Field
	 */
	public function setListRouteFunction($function) { return $this->_set('listRouteFunction', $function); }
	
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
	 * Sets the model to use for fields with type 'multiple' or 'fk'
	 * 
	 * @param string $model
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setModelName($model) { return $this->_set('modelName', $model); }
	
	/**
	 * Sets the parent field if it is different from the model name for fields with type 'multiple'
	 * 
	 * @param string $field
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setParent($field) { return $this->_set('parent', $field); }
	
	/**
	 * Sets the child field name if it is different from the model name for fields with type 'multiple'
	 * 
	 * @param string $field
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setChildField($field) { return $this->_set('childField', $field); }
    
	/**
	 * Hide this field in iframe (when adding from multiple field)
	 * 
	 * @param boolean $hidden
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setHideInIframe($hidden) { return $this->_set('hideInIframe', $hidden); }
	
	/**
	 * Shows this field only when another field has a certain value
	 * 
	 * @param string $field
	 * @param mixed $value
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function addShowOnlyWhen($field, $value) {
		if (!is_array($value)) {
			$value = array($value);
		}
		$showOnlyWhen = ($this->has('showOnlyWhen') ? $this->get('showOnlyWhen') : array());
		if (isset($showOnlyWhen[$field])) {
			$showOnlyWhen[$field] = array_merge($showOnlyWhen[$field], $value);
		} else {
			$showOnlyWhen[$field] = $value;
		}		
		return $this->_set('showOnlyWhen', $showOnlyWhen);	
	}
	
	/**
	 * Sorts this field accoding to a dynamic sorting rule
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return Ajde_Crud_Options_Fields_Field
	 */
	public function addDynamicSort($field, $value) {
		if (!is_array($value)) {
			$value = array($value);
		}
		$dynamicSort = ($this->has('dynamicSort') ? $this->get('dynamicSort') : array());
		if (isset($dynamicSort[$field])) {
			$dynamicSort[$field] = array_merge($dynamicSort[$field], $value);
		} else {
			$dynamicSort[$field] = $value;
		}
		return $this->_set('dynamicSort', $dynamicSort);
	}
	
    /**
	 * Use a simple selector for fields with type 'multiple'
	 * 
	 * @param boolean $simple
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setSimpleSelector($simple) { return $this->_set('simpleSelector', $simple); }
	
	/**
	 * Hides the main column (for fields with type 'multiple')
	 * 
	 * @param boolean $hide
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setHideMainColumn($hide) {
		return $this->_set('hideMainColumn', $hide);
	}
	
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
	 * Defines a sort field on the foreign table
	 * 
	 * @param string $table
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function addSortField($field) {
		$fields = ($this->has('tableFields') ? $this->get('tableFields') : array());
		$fields[] = array('name' => $field, 'type' => 'sort');
		$this->setSortBy($field);
		return $this->_set('tableFields', $fields);	
	}
	
	/**
	 * Adds an image column to the cross reference table (for fields with type 'multiple')
	 * 
	 * @param string $field
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function addMetaField($metaId, $function = false) {
		$fields = ($this->has('tableFields') ? $this->get('tableFields') : array());
		$fields[] = array('name' => $metaId, 'type' => 'meta', 'function' => $function);
		return $this->_set('tableFields', $fields);
	}
	
	/**
	 * Sort the foreign table by this field
	 * 
	 * @param string $table
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function setSortBy($field) {
		return $this->_set('sortBy', $field);
	}	
	
	/**
	 * Prefills the foreign record with this value when adding/editing
	 * 
	 * @param string $field
	 * @param string $value
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
	public function addPrefillField($field, $value) {
		$fields = ($this->has('prefillField') ? $this->get('prefillField') : array());
		$fields[$field] = $value;
		return $this->_set('prefillField', $fields);
	}
    
    /**
	 * Use an image for spatial field instead of Google Maps or
	 * use an image in fk selected field (with setUsePopupSelector=true)
	 * 
	 * @param boolean $image
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
    public function setUseImage($image) {
        return $this->_set('useImage', $image);
    }
	
	/**
	 * Use this image for spatial field instead of Google Maps
	 * 
	 * @param string $image
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
    public function setLayerImage($image) {
        return $this->_set('layerImage', $image);
	}
    
    /**
	 * Type db column for fields with type 'media'
	 * 
	 * @param string $fieldname
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
    public function setTypeField($fieldname) {
        return $this->_set('typeField', $fieldname);
    }
    
    /**
	 * Type db column for fields with type 'media'
	 * 
	 * @param string $fieldname
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
    public function setThumbField($fieldname) {
        return $this->_set('thumbField', $fieldname);
    }
	
	/**
	 * Filename db column for fields with type 'media'
	 * 
	 * @param string $fieldname
	 * @return Ajde_Crud_Options_Fields_Field 
	 */
    public function setFilenameField($fieldname) {
        return $this->_set('filenameField', $fieldname);
    }
	
	/**
	 * Use popup list selector for fk or multiple type fields
	 * 
	 * @param boolean $use
	 * @return Ajde_Crud_Options_Fields_Field
	 */
	public function setUsePopupSelector($use) {
		return $this->_set('usePopupSelector', $use);
	}
	
	/**
	 * Use this package for fields with type icon
	 *
	 * @param string $package
	 * @return Ajde_Crud_Options_Fields_Field
	 */
	public function setIconPackage($package) {
		return $this->_set('iconPackage', $package);
	}

    /**
     * Use max chars in list view
     *
     * @param string $package
     * @return Ajde_Crud_Options_Fields_Field
     */
    public function setMaxChars($maxChars) {
        return $this->_set('maxChars', $maxChars);
    }
	
	/**
	 * Sets the fields to clone when creating a translation
	 *
	 * @param array $fields
	 * @return Ajde_Crud_Options_Fields_Field
	 */
	public function setCloneFields($array) { return $this->_set('cloneFields', $array); }
}