<?php

abstract class Ajde_Object_Singleton extends Ajde_Object_Magic implements Ajde_Object_SingletonInterface
{
    protected static $__pattern = self::OBJECT_PATTERN_SINGLETON;

    public static function __getPattern()
    {
        return self::$__pattern;
    }

    // Do not allow an explicit call of the constructor
    protected function __construct()
    {
    }

    // Do not allow the clone operation
    final private function __clone()
    {
    }
}
