<?php

class Ajde_Crud_Field_Multiple extends Ajde_Crud_Field
{
	/**
	 *
	 * @var Ajde_Collection
	 */
	private $_collection;
	
	/**
	 *
	 * @var Ajde_Model
	 */
	private $_model;
	
	/**
	 * 
	 * @return string 
	 */	
	public function getModelName()
	{
		if ($this->hasModelName()) {
			return $this->get('modelName');
		} else {
			return $this->getName();
		}
	}
	
	/**
	 *
	 * @return Ajde_Collection
	 */
	public function getCollection()
	{
		if (!isset($this->_collection)) {
			$collectionName = ucfirst($this->getModelName()) . 'Collection';
			$this->_collection = new $collectionName;
		}
		return $this->_collection;
	}
	
	/**
	 *
	 * @return Ajde_Model 
	 */
	public function getModel()
	{
		if (!isset($this->_model)) {
			$modelName = ucfirst($this->getModelName()) . 'Model';
			$this->_model = new $modelName;
		}
		return $this->_model;
	}
	
	public function getValues()
	{		
		if ($this->hasFilter()) {
			$filter = $this->getFilter();
			$group = new Ajde_Filter_WhereGroup();
			foreach($filter as $rule) {
				$group->addFilter(new Ajde_Filter_Where($this->getModel()->getDisplayField(), Ajde_Filter::FILTER_EQUALS, $rule, Ajde_Query::OP_OR));
			}
			$this->getCollection()->addFilter($group);
		}

		if ($this->hasAdvancedFilter()) {
			$filters = $this->getAdvancedFilter();
			$group = new Ajde_Filter_WhereGroup();
			foreach($filters as $filter) {
				if ($filter instanceof Ajde_Filter_Where) {
					$group->addFilter($filter);
				} else {
					$this->getCollection()->addFilter($filter);
				}		
			}
			$this->getCollection()->addFilter($group);
		}
		
		$this->getCollection()->orderBy($this->getModel()->getDisplayField());
//		$return = array();
//		foreach($this->getCollection() as $model) {
//			$return[(string) $model] = $model->get($model->getDisplayField());
//		}
//		return $return;
		return $this->getCollection();
	}
	
	public function getChildren()
	{		
		if ($this->hasCrossReferenceTable()) {
			$childPk = $this->getModel()->getTable()->getPK();
			$parent = (string) $this->getCrud()->getModel()->getTable();
			$parentId = $this->getCrud()->getModel()->getPK();			
			$crossReferenceTable = $this->getCrossReferenceTable();
			
			// TODO: implement $this->getAdvancedFilter() filters in subquery
			$subQuery = new Ajde_Db_Function('(SELECT ' . $this->getModelName() . ' FROM ' . $crossReferenceTable . ' WHERE ' . $parent . ' = ' . (integer) $parentId . ')');
			$collection = $this->getCollection();
			$collection->reset();
			$collection->addFilter(new Ajde_Filter_Where($childPk, Ajde_Filter::FILTER_IN, $subQuery));
		} else {
			$collection = $this->getCollection();
			$collection->addFilter(new Ajde_Filter_Where((string) $this->_crud->getModel()->getTable(), Ajde_Filter::FILTER_EQUALS, (string) $this->_crud->getModel()));
			
			if ($this->hasAdvancedFilter()) {
				$filters = $this->getAdvancedFilter();
				$group = new Ajde_Filter_WhereGroup();
				foreach($filters as $filter) {
					if ($filter instanceof Ajde_Filter_Where) {
						$group->addFilter($filter);
					} else {
						$this->getCollection()->addFilter($filter);
					}		
				}
				$collection->addFilter($group);
			}
			
		}
		return $collection;
	}
}