<?php

class Ajde_Resource_Remote extends Ajde_Resource
{

	public function  __construct($type, $url, $arguments = '')
	{
		$this->setUrl($url);
		$this->setArguments($arguments);
		parent::__construct($type);
	}

	public function getFilename()
	{
		return $this->getUrl();
	}

	protected function getLinkUrl()
	{
		return $this->getUrl();
	}

}