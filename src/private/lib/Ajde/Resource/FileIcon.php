<?php

/*
 * Code from CambioCMS project
 * @link http://code.google.com/p/cambiocms/source/browse/trunk/cms/includes/602_html.image.php
 */ 

class Ajde_Resource_FileIcon extends Ajde_Resource
{	
	private $_iconCdn = 'http://p.yusukekamiyamane.com/icons/search/fugue/icons/%s.png';
	private $_iconDictionary = array(
		'*'   => 'document',
		'jpg' => 'document-image', 'gif' => 'document-image', 'png' => 'document-image', 'bmp' => 'document-image',
		'xls' => 'document-excel', 'xlsx' => 'document-excel',
		'ppt' => 'document-powerpoint', 'pptx' => 'document-powerpoint',
		'doc' => 'document-word', 'docx' => 'document-word',
		'pdf' => 'document-pdf',
		'mp3' => 'document-music', 'wav' => 'document-music',
	);
	
	public function __construct($fileExtension)
	{
		$this->set('extension', $fileExtension);
	}
	
	public static function _($fileExtension)
	{
		$tmp = new self($fileExtension);
		return (string) $tmp;
	}
	
	public function getFilename()
	{
		return $this->getLinkUrl();
	}
	
	public function __toString()
	{
		return $this->getFilename();
	}
	
	protected function getLinkUrl()
	{
		return sprintf($this->_iconCdn, isset($this->_iconDictionary[$this->getExtension()]) ? $this->_iconDictionary[$this->getExtension()] : $this->_iconDictionary['*']);
	}	
}