<?php

class Ajde_Document_Format_Json extends Ajde_Document
{
	protected $_cacheControl = self::CACHE_CONTROL_NOCACHE;
	protected $_contentType = 'application/json';
	protected $_maxAge = 0; // access

	public function render()
	{
		Ajde_Cache::getInstance()->disable();
		Ajde::app()->getDocument()->setLayout(new Ajde_Layout('empty'));		
		return parent::render();
	}
	
	public function getBody()
	{
		return json_encode($this->get('body'));
	}
}