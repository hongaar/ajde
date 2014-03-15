<?php

class Ajde_Crud_Field_Header extends Ajde_Crud_Field
{
	public function getHtml()
	{
		$template = $this->_getInputTemplate();
		$template->assign('field', $this);
		return $template->render();
	}
	
	public function getInput($id = null)
	{
		return '';
	}
}