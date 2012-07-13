<?php

class Ajde_Crud_Field_Fk extends Ajde_Crud_Field
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
	
	public function getCollection()
	{
		if (!isset($this->_collection)) {
			$collectionName = ucfirst($this->getName()) . 'Collection';
			$this->_collection = new $collectionName;
		}
		return $this->_collection;
	}
	
	public function getModel()
	{
		if (!isset($this->_model)) {
			$modelName = ucfirst($this->getName()) . 'Model';
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
		$return = array();
		foreach($this->getCollection() as $model) {
			$return[(string) $model] = $model->get($model->getDisplayField());
		}
		return $return;
	}
}