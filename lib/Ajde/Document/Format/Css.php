<?php

class Ajde_Document_Format_Css extends Ajde_Document
{
	protected $_cacheControl = self::CACHE_CONTROL_PUBLIC;
	protected $_contentType = 'text/css';

	public function render()
	{
		Ajde::app()->getDocument()->setLayout(new Ajde_Layout('empty'));
		Ajde::app()->getResponse()->removeHeader('Set-Cookie');
		if (Ajde::app()->getRequest()->getRoute()->getAction() == 'resourceCompressed') {
			$this->registerDocumentProcessor('css', 'compressor');
		} else {
			$this->registerDocumentProcessor('css');
		}
		return parent::render();		
	}	
}