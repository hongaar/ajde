<?php

class Ajde_Lang_Adapter_Ini extends Ajde_Lang_Adapter_Abstract
{
	// TODO: cache .ini files!
	
	public function get($ident, $module = null)
	{
		$module = $this->getModule($module);
		
		$lang = Ajde_Lang::getInstance()->getLang();
		$iniFilename = LANG_DIR . $lang . '/' . $module . '.ini';
		if (is_file($iniFilename)) {
			$book = parse_ini_file($iniFilename);
			if (array_key_exists($ident, $book)) {
				return $book[$ident];
			}
		}
		return $ident;
	}
}