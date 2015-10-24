<?php
include 'Markdown/markdown.php'; 

class Ajde_Component_Markdown extends Ajde_Component
{
	public static function processStatic(Ajde_Template_Parser $parser, $attributes)
	{
		$instance = new self($parser, $attributes);
		return $instance->process();
	}
	
	protected function _init()
	{
		return array(
			'text' => 'toHtml',
		);
	}
	
	public function process()
	{
		switch($this->_attributeParse()) {
		case 'toHtml':
			$text = $this->attributes['text'];
						
			return Markdown($text);
			break;
		}
		// TODO:
		throw new Ajde_Component_Exception('Missing required attributes for component call');	
	}
}