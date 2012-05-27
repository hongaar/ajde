<?php

class Ajde_Lang extends Ajde_Object_Singleton
{
	protected $_adapter = null;
	protected $_lang;
	
	/**
	 * @return Ajde_Lang
	 */
	public static function getInstance()
	{
    	static $instance;
    	return $instance === null ? $instance = new self : $instance;
	}
		
	protected function __construct()
	{
		$this->setLang($this->detect());
	}
	
	public function getLang()
	{
		return $this->_lang;
	}
	
	public function getShortLang()
	{
		return substr($this->_lang, 0, 2);
	}
	
	public function setLang($lang)
	{
		setlocale(LC_ALL, $lang, $lang.'.utf8', $lang.'.UTF8', $lang.'utf-8', $lang.'.UTF-8');
		$this->_lang = $lang;
	}

	public function getAvailableLang($langCode)
	{
		$availableLangs = $this->getAvailable();
		$availableShortLangs = array();
		foreach($availableLangs as $availableLang) {
			$availableShortLangs[substr($availableLang, 0, 2)] = $availableLang;
		}
		if (in_array($langCode, $availableLangs)) {
			return $langCode;
		}
		if (array_key_exists($langCode, $availableShortLangs)) {
			return $availableShortLangs[$langCode];
		}
		return false;
	}
	
	public function setGlobalLang($lang)
	{
		$this->setLang($lang);
		Config::getInstance()->lang_root = Config::getInstance()->site_root . $this->getShortLang() . '/';
	}

	protected function detect()
	{		
		if (Config::get("langAutodetect")) {
			$acceptedLangs = $this->getLanguagesFromHeader();
			foreach($acceptedLangs as $acceptedLang => $priority) {
				if ($langMatch = $this->getAvailableLang($acceptedLang)) {
					return $langMatch;
				}
			}
		}
		return $defaultLang = Config::get("lang");
	}
	
	protected function getLanguagesFromHeader()
	{
		// @source http://www.thefutureoftheweb.com/blog/use-accept-language-header 
		$langs = array();
		if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			// break up string into pieces (languages and q factors)
			preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
			if (count($lang_parse[1])) {
    			// create a list like "en" => 0.8
				$langs = array_combine($lang_parse[1], $lang_parse[4]);
				
				// set default to 1 for any without q factor
				foreach ($langs as $lang => $val) {
					if ($val === '') $langs[$lang] = 1;
				}
				
				// sort list based on value	
        		arsort($langs, SORT_NUMERIC);
			}
		}
		return $langs;
	}
	
	protected function getAvailable()
	{
		$langs = Ajde_FS_Find::findFiles(LANG_DIR, '*');
		$return = array();
		foreach($langs as $lang) {
			$return[] = basename($lang);
		}
		return $return;
	}
	
	/**
	 * @return Ajde_Lang_Adapter_Abstract
	 */
	public function getAdapter()
	{
		if ($this->_adapter === null) {
			$adapterName = 'Ajde_Lang_Adapter_' . ucfirst(Config::get('langAdapter'));
			$this->_adapter = new $adapterName();
		}
		return $this->_adapter;
	}
	
	public static function _($ident, $module = null)
	{
		return self::getInstance()->translate($ident, $module);
	}
	
	public function translate($ident, $module = null)
	{
		return $this->getAdapter()->get($ident, $module);
	}
	
	public function get($ident, $module = null)
	{
		// TODO:
		throw new Ajde_Core_Exception_Deprecated();
		return $this->getAdapter()->get($ident, $module);
	}
}