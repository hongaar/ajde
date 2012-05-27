<?php

class Ajde_Resource_Local_Compressed extends Ajde_Resource
{
	public function  __construct($type, $filename)
	{
		$this->setFilename($filename);
		parent::__construct($type);		
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
		return new Ajde_Resource_Local_Compressed($type, $f);
	}
	
	public function getFingerprint()
	{
		$array = array('f' => $this->getFilename());
		return $this->encodeFingerprint($array);
	}

	public function getLinkUrl()
	{		
		//$hash = md5(serialize($this));
		//$session = new Ajde_Session('AC.Resource');
		//$session->set($hash, $this);
		
		//$url = '_core/component:resourceCompressed/' . $this->getType() . '/' . $hash . '/';
		$url = '_core/component:resourceCompressed/' . urlencode($this->getFingerprint()) . '.' . $this->getType();
		
		if (Config::get('debug') === true)
		{
			$url .= '?file=' . str_replace('%2F', ':', urlencode($this->getFilename()));
		}
		return $url;
	}

	public function getFilename() {
		return $this->get('filename');
	}
}