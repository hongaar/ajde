<?php

abstract class Ajde_Lang_Adapter_Abstract
{
    public static function _($ident, $module = null)
    {
        return self::getInstance()->get($ident, $module);
    }

    public function getModule($module = null)
    {
        if (!$module) {
            foreach(debug_backtrace() as $item) {
                if (!empty($item['class'])) {
                    if (is_subclass_of($item['class'], "Ajde_Controller")) {
                        $module = current(explode('_', Ajde_Component_String::toSnakeCase($item['class'])));
                        break;
                    }
                }
            }
        }
        if (!$module) {
            $module = 'main';
        }
        return $module;
    }

    public function log($ident, $module)
    {
        Ajde_Log::_(
            'Language key [' . $module . '.' . $ident . '] not found for language [' . Ajde_Lang::getInstance()->getLang() . ']',
            Ajde_Log::CHANNEL_INFO,
            Ajde_Log::LEVEL_DEBUG,
            '',
            '',
            strip_tags(Ajde_Exception_Handler::trace(new Ajde_Exception(), Ajde_Exception_Handler::EXCEPTION_TRACE_ONLY)));
    }

    abstract public function get($ident, $module = null);
}