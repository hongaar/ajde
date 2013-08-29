<?php 

class Ajde_Component_Js extends Ajde_Component_Resource
{
	public static function processStatic(Ajde_Template_Parser $parser, $attributes)
	{
		$instance = new self($parser, $attributes);
		return $instance->process();
	}
	
	public function process()
	{
//		TODO: check for required attributes
//		if (!array_key_exists('library', $this->attributes) || !array_key_exists('version', $this->attributes)) {
//			throw new Ajde_Component_Exception();
//		}
		if (array_key_exists('library', $this->attributes)) {
			$this->requireJsLibrary($this->attributes['library'], $this->attributes['version']);
		} elseif (array_key_exists('action', $this->attributes)) {
			$this->requireResource(
				Ajde_Resource_Local::TYPE_JAVASCRIPT,
				$this->attributes['action'],
				issetor($this->attributes['format'], null),
				issetor($this->attributes['base'], null),
				issetor($this->attributes['position'], null),
				issetor($this->attributes['arguments'], '')
			);
		} elseif (array_key_exists('filename', $this->attributes)) {
			$this->requirePublicResource(
				Ajde_Resource_Local::TYPE_JAVASCRIPT,
				$this->attributes['filename'],
				issetor($this->attributes['position'], null),
				issetor($this->attributes['arguments'], '')
			);
		} elseif (array_key_exists('url', $this->attributes)) {
			$this->requireRemoteResource(
				Ajde_Resource_Local::TYPE_JAVASCRIPT,
				$this->attributes['url'],
				issetor($this->attributes['position'], null),
				issetor($this->attributes['arguments'], '')
			);
		}
	}
	
	public function requireJsLibrary($library, $version)
	{
		$url = Ajde_Resource_JsLibrary::getUrl($library, $version);
		$resource = new Ajde_Resource_Remote(Ajde_Resource::TYPE_JAVASCRIPT, $url);
		$this->getParser()->getDocument()->addResource($resource, Ajde_Document_Format_Html::RESOURCE_POSITION_TOP);
	}
}