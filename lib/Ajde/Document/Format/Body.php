<?php

class Ajde_Document_Format_Body extends Ajde_Document
{
	protected $_cacheControl = self::CACHE_CONTROL_PRIVATE;
	protected $_maxAge = 0; // access
	
	public function render()
	{		
		Ajde::app()->getDocument()->setLayout(new Ajde_Layout('empty'));		
		return parent::render();
	}
	
	public function getBody()
	{
		return $this->get('body');
	}
}