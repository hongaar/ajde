<?php

class Ajde_Resource_Local extends Ajde_Resource
{
	private $_filename;
	
	public function  __construct($type, $base, $action, $format = 'html', $arguments = '')
	{
		$this->setBase($base);
		$this->setAction($action);
		$this->setFormat($format);
		$this->setArguments($arguments);
		parent::__construct($type);
	}

	/**
	 *
	 * @param string $type
	 * @param string $base
	 * @param string $action
	 * @param string $format (optional)
	 * @return Ajde_Resource
	 */
	public static function lazyCreate($type, $base, $action, $format = 'html')
	{
		if (self::getFilenameFromStatic($base, $type, $action, $format)) {
			return new self($type, $base, $action, $format);
		}
		return false;
	}

	/**
	 *
	 * @param string $hash
	 * @return Ajde_Resource
	 */
	public static function fromHash($hash)
	{
		// TODO:
		throw new Ajde_Core_Exception_Deprecated();
		$session = new Ajde_Session('AC.Resource');
		return $session->get($hash);
	}
	
	public static function fromFingerprint($type, $fingerprint)
	{
		$array = self::decodeFingerprint($fingerprint);
		extract($array);
		return new Ajde_Resource_Local($type, $b, $a, $f);
	}
	
	public function getFingerprint()
	{
		$array = array('b' => $this->getBase(), 'a' => $this->getAction(), 'f' => $this->getFormat());
		return $this->encodeFingerprint($array);
	}

	public function getBase() {
		return $this->get('base');
	}

	public function getAction() {
		return $this->get('action');
	}

	public function getFormat() {
		return $this->get('format');
	}
	
	public function getArguments() {
		return $this->get('arguments');
	}
	
	protected static function exist($filename)
	{
		if (is_file($filename)) {
			return true;
		}
		return false;

	}

	protected static function _getFilename($base, $type, $action, $format)
	{
		$filename = false;
		$formatResource = $base . 'res/' . $type . '/' . $action . '.' . $format . '.' . $type;
		if (self::exist($formatResource)) {
			$filename = $formatResource;
		} else {
			$noFormatResource = $base . 'res/' . $type . '/' . $action . '.' . $type;
			if (self::exist($noFormatResource)) {
				$filename = $noFormatResource;
			}
		}		
		return $filename;
	}

	public function getFilename()
	{
		if (!isset($this->_filename)) {
			$this->_filename = $this->_getFilename($this->getBase(), $this->getType(), $this->getAction(), $this->getFormat());
		}
		if (!$this->_filename) {
			// TODO:
			throw new Ajde_Exception(sprintf('Resource %s could not be found',
					$this->getBase() . 'res/' . $this->getType() . '/' . $this->getAction() . '[.' . $this->getFormat() . '].' . $this->getType()));
		}
		return $this->_filename;
	}

	public static function getFilenameFromStatic($base, $type, $action, $format)
	{
		return self::_getFilename($base, $type, $action, $format);
	}

	protected function getLinkUrl()
	{
		//$hash = md5(serialize($this));
		//$session = new Ajde_Session('AC.Resource');
		//$session->set($hash, $this);
		
		//$url = '_core/component:resourceLocal/' . $this->getType() . '/' . $hash . '/';
		$url = '_core/component:resourceLocal/' . urlencode($this->getFingerprint()) . '.' . $this->getType();

		if (Config::get('debug') === true)
		{
			$url .= '?file=' . str_replace('%2F', ':', urlencode($this->getFilename()));
		}
		return $url;
	}
}