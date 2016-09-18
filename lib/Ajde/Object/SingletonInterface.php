<?php

interface Ajde_Object_SingletonInterface
{
    /**
     * Example:.
     *
     * public static function getInstance()
     * {
     *    static $instance;
     *    return $instance === null ? $instance = new self : $instance;
     * }
     *
     * @abstract
     */
    public static function getInstance();
}
