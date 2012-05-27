<?php 

class Ajde_Component_Form extends Ajde_Component
{
	public static function processStatic(Ajde_Template_Parser $parser, $attributes)
	{
		$instance = new Ajde_Component_Form($parser, $attributes);
		$t = new stdClass(); // Force unique object hash, see http://www.php.net/manual/es/function.spl-object-hash.php#76220
		return $instance->process();
	}
	
	protected function _init()
	{
		return array(
			'ajax' => 'ajax',
			'route' => 'form',
			'upload' => 'upload' 
		);
	}
	
	public function process()
	{
		switch($this->_attributeParse()) {
		case 'form':
			$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/component:form'));
			
			$controller->setFormAction($this->attributes['route']);
			$controller->setFormId(issetor($this->attributes['id'], spl_object_hash($this)));
			$controller->setExtraClass(issetor($this->attributes['class'], ''));
			$controller->setInnerXml($this->innerXml);
			
			return $controller->invoke();
			break;
		case 'ajax':
			$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/component:formAjax'));
			$formAction = new Ajde_Core_Route($this->attributes['route']);
			$formAction->setFormat(issetor($this->attributes['format'], 'json'));
			
			$controller->setFormAction($formAction->__toString());
			$controller->setFormFormat(issetor($this->attributes['format'], 'json'));
			$controller->setFormId(issetor($this->attributes['id'], spl_object_hash($this)));
			$controller->setExtraClass(issetor($this->attributes['class'], ''));
			$controller->setInnerXml($this->innerXml);
			
			return $controller->invoke();
			break;
		case 'upload':
			$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/component:formUpload'));
			
			if (!isset($this->attributes['options']) ||
					!isset($this->attributes['options']['saveDir']) ||
					!isset($this->attributes['options']['extensions'])) {
				// TODO:
				throw new Ajde_Component_Exception('Options saveDir and extensions must be set for AC.Form.Upload');
			}
			
			$controller->setName($this->attributes['name']);
			$controller->setOptions($this->attributes['options']);
			$controller->setInputId(issetor($this->attributes['id'], spl_object_hash($this)));
			$controller->setExtraClass(issetor($this->attributes['class'], ''));
			
			return $controller->invoke();
			break;
		}
		// TODO:
		throw new Ajde_Component_Exception();	
	}
	
}