<?php	

class Ajde_Template_Parser_Xhtml_Element extends DOMElement
{
	public function inACNameSpace()
	{
		return substr($this->nodeName, 0, 3) === Ajde_Component::AC_XMLNS . ':';	
	}
	
	public function inAVNameSpace()
	{
		return substr($this->nodeName, 0, 3) === Ajde_Component::AV_XMLNS . ':';	
	}
	
	public function processVariable(Ajde_Template_Parser $parser)
	{
		$variableName = str_replace(Ajde_Component::AV_XMLNS . ':', '', $this->nodeName);
		if (!$parser->getTemplate()->hasAssigned($variableName)) {
			 throw new Ajde_Exception("No variable with name '" . $variableName . "' assigned to template.", 90019);
		}
		$contents = (string) $parser->getTemplate()->getAssigned($variableName);
		/* @var $doc DOMDocument */
		$doc = $this->ownerDocument;
		$cdata = $doc->createCDATASection($contents);
		$this->appendChild($cdata);
	}
	
	public function processComponent(Ajde_Template_Parser $parser)
	{
		$component = Ajde_Component::fromNode($parser, $this);
		$contents = $component->process();
		/* @var $doc DOMDocument */
		$doc = $this->ownerDocument;
		$cdata = $doc->createCDATASection($contents);
		$this->appendChild($cdata);
	}
	
}