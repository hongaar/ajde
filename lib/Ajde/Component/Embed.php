<?php 

class Ajde_Component_Embed extends Ajde_Component
{
	public static function processStatic(Ajde_Template_Parser $parser, $attributes)
	{
		$instance = new self($parser, $attributes);
		return $instance->process();
	}
	
	protected function _init()
	{
		return array(
			'url' => 'render'
		);
	}
	
	public function process()
	{
		switch($this->_attributeParse()) {
		case 'render':			
			$image = new Ajde_Resource_Image($this->attributes['filename']);
			$image->setWidth($this->attributes['width']);
			$image->setHeight($this->attributes['height']);
			$image->setCrop(Ajde_Component_String::toBoolean($this->attributes['crop']));
						
			$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/component:imageBase64'));
			$controller->setImage($image);
			$controller->setWidth(issetor($this->attributes['width'], null));
			$controller->setHeight(issetor($this->attributes['height'], null));
			$controller->setExtraClass(issetor($this->attributes['class'], ''));
					
			return $controller->invoke();
			break;
		}
		// TODO:
		throw new Ajde_Component_Exception('Missing required attributes for component call');	
	}
}