<?php

abstract class Ajde_Crud_Cms_Type extends Ajde_Object_Standard
{	
	public function __construct() {
		
	}
	
	public function decorateCrudOptions(Ajde_Crud_Options $options)
	{
		$this->options = $options;
	}
}