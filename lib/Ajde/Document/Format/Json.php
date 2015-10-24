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
		$body = json_encode($this->get('body'));
		if (Config::get('debug')) {
			if (Ajde_Dump::getAll()) {
				foreach(Ajde_Dump::getAll() as $source => $var) {
					//if ($var[1] === true) { $expand = true; }
					$body .= "<pre class='xdebug-var-dump'>" . var_export($var[0], true) . "</pre>";
				}
			}
		}
		return $body;
	}
}