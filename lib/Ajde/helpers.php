<?php

class Ajde extends Ajde_Application
{
}

class Config extends Ajde_Config
{
}

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
    Ajde_Dump::dump($var, $expand);
}

/**
 * Translates the string with Ajde_Lang::translate
 *
 * @param string $ident
 * @param string $module
 * @return string
 */
function __($ident, $module = null)
{
    return Ajde_Lang::getInstance()->translate($ident, $module);
}

/**
 * Escapes the string with Ajde_Component_String::escape
 *
 * @param string $var
 * @return string
 */
function _e($var)
{
    return Ajde_Component_String::escape($var);
}

/**
 * Cleans the string with Ajde_Component_String::clean
 *
 * @param string $var
 * @return string
 */
function _c($var)
{
    return Ajde_Component_String::clean($var);
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
    return config($param);
}
