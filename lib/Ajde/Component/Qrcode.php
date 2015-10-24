<?php 

class Ajde_Component_Qrcode extends Ajde_Component
{
	public static function processStatic(Ajde_Template_Parser $parser, $attributes)
	{
		$instance = new self($parser, $attributes);
		return $instance->process();
	}
	
	protected function _init()
	{
		return array(
			'text' => 'html',
		);
	}
	
	public function process()
	{
		switch($this->_attributeParse()) {
		case 'html':
			$qr = new Ajde_Resource_Qrcode($this->attributes['text']);
			
			$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/component:qrcode'));
			$controller->setQrcode($qr);			
			return $controller->invoke();
			break;
		}
		// TODO:
		throw new Ajde_Component_Exception('Missing required attributes for component call');	
	}
}