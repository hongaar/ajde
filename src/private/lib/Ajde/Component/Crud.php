<?php 

class Ajde_Component_Crud extends Ajde_Component
{
	public static function processStatic(Ajde_Template_Parser $parser, $attributes)
	{
		$instance = new self($parser, $attributes);
		return $instance->process();
	}
	
	protected function _init()
	{
		return array(
			'list' => 'list',
			'edit' => 'edit'
		);
	}
	
	public function process()
	{
		switch($this->_attributeParse()) {
		case 'list':
			$options = issetor($this->attributes['options'], array());
			$crud = new Ajde_Crud($this->attributes['model'], $options);
			$crud->setAction('list');
			return $crud;				
			break;
		case 'edit':
			$options = issetor($this->attributes['options'], array());
			$id = issetor($this->attributes['id'], null);
			$crud = new Ajde_Crud($this->attributes['model'], $options);
			$crud->setId($id);
			$crud->setAction('edit');
			return $crud;
			break;
		}		
		// TODO:
		throw new Ajde_Component_Exception();	
	}
}