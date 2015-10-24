<?php	

class Ajde_Template_Parser_Xhtml extends Ajde_Template_Parser
{
	protected $_defaultNS = null;
	protected $_acNS = null;
	protected $_avNS = null;
	
	public function parse()
	{
		// Get the XHTML
		$xhtml = $this->_getContents();
		$doc = new DOMDocument();
		$doc->registerNodeClass('DOMElement', 'Ajde_Template_Parser_Xhtml_Element');
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		try {
			$doc->loadXML($xhtml);
		} catch (ErrorException $e) {
			// TODO:
			//return false;
			throw new Ajde_Exception('Xhtml Parser error: ' . $e->getMessage());
		}
		
		// Get the root element
		/* @var $root DOMNode */
		$root = $doc->documentElement;		
		$this->_defaultNS = $root->lookupNamespaceURI(null);
		$this->_acNS = $root->lookupNamespaceURI(Ajde_Component::AC_XMLNS);
		$this->_avNS = $root->lookupNamespaceURI(Ajde_Component::AV_XMLNS);
		
		// Ajde_Component processing
		$processed = $this->_process($root);
		
		// Return the inner XML of root element (exclusive)
		$xml = $this->innerXml($processed);
		
		// Break out the CDATA
		$return = $this->_breakOutCdata($xml);
		
		return $return;
	}
	
	protected function _process(DOMElement $root)
	{
		foreach($root->getElementsByTagNameNS($this->_acNS, '*') as $element) {
			/* @var $element Ajde_Template_Parser_Xhtml_Element */
			if ($element->inACNameSpace()) {
				$element->processComponent($this);
			} elseif ($element->inAVNameSpace()) {
				$element->processVariable($this);
			}
		}
		return $root;
	}
	
	protected function _breakOutCdata($xml) 
	{
		$patterns = array(
			'%<a[cv]:.+?<!\[CDATA\[%',
			'%\]\]>.*</a[cv]:.+?>%'
		);
		$return = preg_replace($patterns, '', $xml);
		return $return ;
	}
	
	public function innerXml(DOMElement $node)
	{
		$return = '';
		foreach ($node->childNodes as $element) { 
			$return .= $element->ownerDocument->saveXML($element);
		}
		return $return;
	}
}