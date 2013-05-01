<?php

class Ajde_Crud_Cms_Type_Text extends Ajde_Crud_Cms_Type
{
	/**
	 *
	 * @var Ajde_Crud_Options 
	 */
	protected $options;
	
	public function __construct() {
		
	}
	
	public function decorateCrudOptions(Ajde_Crud_Options $options)
	{
		$this->options = $options;
	}
}