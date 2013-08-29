<?php

class SettingModel extends Ajde_Model
{
	protected $_autoloadParents = false;
	protected $_displayField = 'name';	
	protected $_hasMeta = true;
	
	private static $_cache = array();
	
	public static function byName($name) {
		if (isset(self::$_cache[$name])) {
			return self::$_cache[$name];
		}
		
		$niceName = str_replace('_', ' ', $name);
		$settings = new SettingCollection();		
		$settings->addFilter(new Ajde_Filter_Join('setting_meta', 'setting_meta.setting', 'setting.id'));
		$settings->addFilter(new Ajde_Filter_Join('meta', 'meta.id', 'setting_meta.meta'));
		$settings->addFilter(new Ajde_Filter_Where('meta.name', Ajde_Filter::FILTER_EQUALS, $niceName));
		$settings->getQuery()->addSelect('setting_meta.value');
		
		if ($settings->count()) {
			$setting = $settings->current();
			self::$_cache[$name] = $setting->get('value');
			return $setting->get('value');
		}
		return false;
	}
}
