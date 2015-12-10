<?php

/**
 * The only thing missing in PHP < 5.3
 * In PHP 5.3 you can use: return $test ?: false;
 * This translates in Ajde to return issetor($test);
 *
 * @param $what
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
 * @param $var
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
