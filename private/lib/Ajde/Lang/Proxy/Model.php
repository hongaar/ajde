<?php

abstract class Ajde_Lang_Proxy_Model extends Ajde_Model
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
	
	public function getLanguage()
	{
		return $this->get($this->languageField);
	}
	
	public function isTranslatable()
	{
		$pk = $this->getPK();
		return !empty($pk);
	}
	
	/**
	 * 
	 * @param string $lang
	 * @return Ajde_Lang_Proxy_Acl_Model|boolean
	 */
	public function getTranslated($lang)
	{
		$modelName = $this->toCamelCase($this->_tableName) . 'Model';
		$translation = new $modelName();
		$exist = $translation->loadByFields(array(
				$this->languageRootField => $this->getPK(),
				$this->languageField => $lang
			));
		return $exist ? $translation : false;
	}
	
	/**
	 * 
	 * @param string $lang
	 * @return Ajde_Lang_Proxy_Acl_Model
	 */
	public function getTranslatedLazy($lang)
	{
		$translation = $this->getTranslated($lang);
		return $translation ? $translation : $this;
	}
	
	/**
	 * 
	 * @return Ajde_Model_I18n|boolean
	 */
	public function getRootLang()
	{
		if ($this->hasNotEmpty($this->languageRootField)) {
			$this->loadParent($this->languageRootField);
			return $this->get($this->languageRootField);
		} 
		return false;
	}
	
	public function getTranslations()
	{
		$rootLang = $this->getRootLang();
		if (!$rootLang) {
			$rootLang = $this;
		}
		
		$collection = $this->getCollection();
		/* @var $collection Ajde_Lang_Proxy_Acl_Collection */
		
		$collection->addFilter(new Ajde_Filter_Where($this->languageRootField, Ajde_Filter::FILTER_EQUALS, $rootLang->getPK()));
		return $collection;
	}
	
	protected function _load($sql, $values)
	{
		$return = parent::_load($sql, $values);
		if ($return) {
			// get translation
			$lang = Ajde_Lang::getInstance();
			if ( $translation = $this->getTranslated($lang->getLang()) ) {
				/* @var $translation Ajde_Lang_Proxy_Acl_Model */
				$this->reset();
				$this->loadFromValues($translation->values());
				
			}
		}
		return $return;		
	}
}