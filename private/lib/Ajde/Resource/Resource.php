<?php

abstract class Ajde_Resource extends Ajde_Object_Standard
{
	const TYPE_JAVASCRIPT	= 'js';
	const TYPE_STYLESHEET	= 'css';
	
	public function  __construct($type)
	{
		$this->setType($type);
	}
	
	public function __toString()
	{
		return implode(", ", $this->_data);
	}
	
	abstract public function getFilename();
	abstract protected function getLinkUrl();

	public function getType() {
		return $this->get('type');
	}
	
	public function setPosition($position) {
		$this->set('position', $position);
	}
	
	public function getPosition() {
		return $this->get('position');
	}

	protected function _getLinkTemplateFilename()
	{
		$format = $this->hasFormat() ? $this->getFormat() : null;
		return self::getLinkTemplateFilename($this->getType(), $format);
	}

	public static function getLinkTemplateFilename($type, $format = 'null')
	{
		if (Ajde::app()->getDocument()->hasLayout()) {
			$layout = Ajde::app()->getDocument()->getLayout();
		} else {
			$layout = new Ajde_Layout(Config::get("layout"));
		}
		$format = issetor($format, 'html');
		
		$dirPrefixPatterns = array(
				APP_DIR, CORE_DIR
		);
		foreach($dirPrefixPatterns as $dirPrefixPattern) {
			$prefixedLayout = $dirPrefixPattern . LAYOUT_DIR;
			if (self::exist($prefixedLayout . $layout->getName() . '/link/' . $type . '.' . $format . '.php')) {
				return $prefixedLayout . $layout->getName() . '/link/' . $type . '.' . $format . '.php';
			}
		}
		return false;
	}
	
	protected static function exist($filename)
	{
		if (is_file($filename)) {
			return true;
		}
		return false;
	}
	
	public static function encodeFingerprint($array)
	{
		return self::_urlEncode(serialize($array));
	}
	
	public static function decodeFingerprint($fingerprint)
	{
		return unserialize(self::_urlDecode($fingerprint));
	}
	
	public static function _urlDecode($string) { 
//		return self::_rotUrl($string);
		return base64_decode($string);
	}
	
	public static function _urlEncode($string) { 
//		return self::_rotUrl($string);
		return base64_encode($string);
	}
	
	public static function _rotUrl($string) { 
		return strtr($string, 
			'/-:?=&%#{}"; QXJKVWPYRHGB abcdefghijklmnopqrstuv123456789ACDEFILMNOSTUwxyz', 
			'QXJKVWPYRHGB /-:?=&%#{}"; 123456789ACDEFILMNOSTUabcdefghijklmnopqrstuvwxyz'); 
	}

	public function getLinkCode()
	{
		ob_start();

		// variables for use in included link template
		$url = $this->getLinkUrl();		
		$arguments = $this->hasArguments() ? $this->getArguments() : '';

		// create temporary resource for link filename
		$linkFilename = $this->_getLinkTemplateFilename();

		// TODO: performance gain?
		// Ajde_Cache::getInstance()->addFile($linkFilename);
        if ($linkFilename) {
		    include $linkFilename;
        } else {
            throw new Ajde_Exception('Link filename for ' . $url . ' not found');
        }

		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
	
	public function getContents() {
		ob_start();

        $filename = $this->getFilename();

		Ajde_Cache::getInstance()->addFile($filename);
        if (is_file($filename)) {
		    include $filename;
        }
		
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}
}