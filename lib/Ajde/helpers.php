<?php

class Ajde extends Ajde_Application {}

class Config extends Ajde_Config {}

class Dump extends Ajde_Dump {}

class Lang extends Ajde_Lang {}

class Str extends Ajde_Component_String {}

class Arr extends Ajde_Core_Array {}

/**
 * Return value when it is set, or something else otherwise.
 *
 * @param      $what
 * @param null $else
 * @return null
 */
function issetor(&$what, $else = null)
{
    // @see http://fabien.potencier.org/article/48/the-php-ternary-operator-fast-or-not
    if (isset($what)) {
        return $what;
    } else {
        return $else;
    }
}

/**
 * @param           $var
 * @param bool|true $expand
 */
function dump($var, $expand = true)
{
    Dump::dump($var, $expand);
}

/**
 * Translates the string with Ajde_Lang::translate
 *
 * @param string $ident
 * @param string $module
 * @return string
 */
function trans($ident, $module = null)
{
    return Lang::trans($ident, $module);
}

/**
 * Escapes the string with Ajde_Component_String::escape
 *
 * @param string $var
 * @return string
 */
function esc($var)
{
    return Str::escape($var);
}

/**
 * Cleans the string with Ajde_Component_String::clean
 *
 * @param string $var
 * @return string
 */
function clean($var)
{
    return Str::clean($var);
}

/**
 * Shortcut to retrieve config param
 *
 * @param $param
 * @return mixed
 * @throws Ajde_Exception
 */
function config($param)
{
    return Config::get($param);
}
