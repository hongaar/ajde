<?php

class Ajde_Document_Format_Js extends Ajde_Document
{
	protected $_cacheControl = self::CACHE_CONTROL_PUBLIC;
	protected $_contentType = 'text/javascript';

	public function render()
	{
		Ajde::app()->getDocument()->setLayout(new Ajde_Layout('empty'));		
		Ajde::app()->getResponse()->removeHeader('Set-Cookie');
		return parent::render();
	}
}