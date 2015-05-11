<?php

class Ajde_Crud_Options_List_View extends Ajde_Crud_Options
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
	
	public function getPage()				{ return parent::getPage(); }
	public function getPageSize()			{ return parent::getPageSize(); }	
	public function getSearch()				{ return parent::getSearch(); }
	public function getOrderBy()			{ return parent::getOrderBy(); }
	public function getOrderDir()			{ return parent::getOrderDir(); }
	public function getFilter()				{ return parent::getFilter(); }
	
	public function getViewType()			{ return parent::getViewType(); }
	public function getFilterVisible()		{ return parent::getFilterVisible(); }
	public function getDisableFilter()		{ return parent::getDisableFilter(); }
	public function getMainFilter()			{ return parent::getMainFilter(); }
	public function getMainFilterGrouper()	{ return parent::getMainFilterGrouper(); }
	
	/**
	 * Sets the current page
	 * 
	 * @param integer $page
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setPage($page) { return $this->_set('page', $page); }
	
	/**
	 * Sets the page size
	 * 
	 * @param integer $size
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setPageSize($size) { return $this->_set('pageSize', $size); }
	
	/**
	 * Sets a search term
	 * 
	 * @param string $q
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setSearch($q) { return $this->_set('search', $q); }
	
	/**
	 * Sets the ordering field
	 * 
	 * @param string $orderBy
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setOrderBy($orderBy) { return $this->_set('orderBy', $orderBy); }

    /**
     * Sets the parent and sort fields for rendering a tree view
     *
     * @param string $parentField
     * @param string $sortField
     * @return Ajde_Crud_Options_List_View
     * @internal param string $orderBy
     */
	public function setTreeView($parentField, $sortField) { return $this->_set('treeView', array('parent' => $parentField, 'sort' => $sortField)); }
	
	/**
	 * Sets the ordering direction
	 * 
	 * @param string $dir (Ajde_Query::ORDER_ASC|Ajde_Query::ORDER_DESC)
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setOrderDir($dir) { return $this->_set('orderDir', $dir); }
	
	
	
	
	
	/** UI ELEMENTS **/

    /**
     * Sets the view name
     *
     * @param string $name
     * @return Ajde_Crud_Options_List_View
     */
    public function setName($name) { return $this->_set('name', $name); }

	/**
	 * Sets the list view
	 * 
	 * @param enum $type ('grid'|'list')
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setViewType($type) { return $this->_set('viewType', $type); }
	
	/**
	 * Whether or not the filters are visible
	 * 
	 * @param boolean $visible
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setFilterVisible($visible) { return $this->_set('filterVisible', $visible); }
	
	/**
	 * Whether or not the filters can be toggled
	 * 
	 * @param boolean $disable
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setDisableFilter($disable) { return $this->_set('disableFilter', $disable); }
	
	/**
	 * Enable the main list filter for this field
	 * 
	 * @param string $field
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setMainFilter($field) { return $this->_set('mainFilter', $field); }
	
	/**
	 * Sets a main filter grouper
	 * 
	 * @param string $field
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function setMainFilterGrouper($field) { return $this->_set('mainFilterGrouper', $field); }
	
	
	
	
	
	/**
	 * Adds a filter
	 * 
	 * @param string $field
	 * @param string $value
	 * @return Ajde_Crud_Options_List_View 
	 */
	public function addFilter($field, $value) {
		if (!$this->hasFilter()) {
			$this->setFilter(array());
		}
		$filter = $this->get('filter');
		$filter[$field] = $value;
		$this->set('filter', $filter);
		return $this;
	}
}