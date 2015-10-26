<?php

class Ajde_Session_Flash extends Ajde_Object_Static
{
    public static function set($key, $value)
    {
        // Disable the cache, as setting a flashed string means doing some operation with side-effects
        Ajde_Cache::getInstance()->disable();

        $session = new Ajde_Session('AC.Flash');
        $session->set($key, $value);
    }

    public static function get($key)
    {
        $session = new Ajde_Session('AC.Flash');
        if ($session->has($key)) {

            // Disable the cache, as getting a flashed string means outputting some message to the user
            Ajde_Cache::getInstance()->disable();

            return $session->getOnce($key);
        } else {
            return false;
        }
    }

    public static function alert($message)
    {
        self::set('alert', $message);
    }
}
