<?php

class Ajde_Crud_Cms_Meta_Decorator extends Ajde_Object_Standard
{
	/**
	 *
	 * @var Ajde_Crud_Options 
	 */
	protected $options;
	
	protected $fields = array();
	
	protected $activeRow = 0;
	protected $activeColumn = 0;
	protected $activeBlock = 0;
	
	protected $meta = array();
	
	public function __construct() {
		$this->meta = new Ajde_Crud_Cms_Meta();
	}
	
	public function setActiveRow($row)
	{
		$this->activeRow = $row;
	}
	
	public function setActiveColumn($column)
	{
		$this->activeColumn = $column;
	}
	
	public function setActiveBlock($block)
	{
		$this->activeBlock = $block;
	}
	
	public function setOptions(Ajde_Crud_Options $crudOptions)
	{
		$this->options = $crudOptions;
	}
	
	public function decorate()
	{		
		foreach($this->meta->getTypes() as $name => $type) {
			/* @var $type Ajde_Crud_Cms_Meta_Type */
			foreach ($type->getFields() as $key => $field) {
				$this->fields[$key] = $field;
			}
		}
		
		foreach ($this->fields as $key => $field) {
			/* @var $field Ajde_Crud_Options_Fields_Field */
			$this->addField($key, $field->values());
		}
		
	}
		
	protected function addField($key, $options)
	{
		$this->options->_stack['fields'][$key] = $options;
		$this->options->_stack['edit']['layout']['rows'][$this->activeRow]['columns'][$this->activeColumn]['blocks'][$this->activeBlock]['show'][] = $key;
	}
}