<?php

abstract class Ajde_Lang_Proxy_Collection extends Ajde_Collection
{
	protected $languageField = 'lang';
	protected $languageRootField = 'lang_root';
	
	public function getLanguageField()
	{
		return $this->languageField;
	}
	
	public function getLanguageRootField()
	{
		return $this->languageRootField;
	}
}