<?php

class Ajde_Crud_Field_File extends Ajde_Crud_Field
{
	protected function _getHtmlAttributes()
	{
		$attributes = '';
		$attributes .= ' type="hidden" ';
		$attributes .= ' value="' . Ajde_Component_String::escape($this->getValue()) . '" ';			
		return $attributes;		
	}
	public function getSaveDir()
	{
		if (!$this->hasSaveDir()) {
			// TODO:
			throw new Ajde_Exception('saveDir not set for Ajde_Crud_Field_File');
		}
		return parent::getSaveDir();
	}
	
	public function getExtensions()
	{
		if (!$this->hasSaveDir()) {
			// TODO:
			throw new Ajde_Exception('extensions not set for Ajde_Crud_Field_File');
		}
		return parent::getExtensions();
	}
}