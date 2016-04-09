<?php

class SettingCollection extends Ajde_Collection
{
    public static function byName($name)
    {

        $niceName = str_replace('_', ' ', $name);
        $settings = new SettingCollection();
        $settings->addFilter(new Ajde_Filter_Join('setting_meta', 'setting_meta.setting', 'setting.id'));
        $settings->addFilter(new Ajde_Filter_Join('meta', 'meta.id', 'setting_meta.meta'));
        $settings->addFilter(new Ajde_Filter_Where('setting.name', Ajde_Filter::FILTER_EQUALS, $niceName));
        $settings->getQuery()->addSelect('setting_meta.value');
        $settings->getQuery()->addSelect('meta.name AS meta_name');

        $result = [];
        if ($settings->count()) {
            foreach ($settings as $setting) {
                $result[$setting->meta_name] = $setting->value;
            }
        }

        return $result;
    }
}
