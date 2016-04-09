<?php

class Ajde_Config
{
    /**
     * @var Ajde_Config_Repository
     */
    private $repository;

    /**
     * TODO
     */
    public function __construct()
    {
        $this->repository = new Ajde_Config_Repository(CONFIG_DIR);
    }

    /**
     * TODO
     *
     * @return Config
     */
    public static function getInstance()
    {
        static $instance;
        return $instance === null ? $instance = new self : $instance;
    }

    /**
     * TODO
     *
     * @param string $param
     * @return mixed
     * @throws Ajde_Exception
     */
    public static function get($param)
    {
        $instance = self::getInstance();

        if (isset($instance->$param)) {
            return $instance->$param;
        } else {
            throw new Ajde_Exception("Config parameter $param not set", 90004);
        }
    }

    /**
     * TODO
     *
     * @param string $param
     * @param mixed $value
     */
    public static function set($param, $value)
    {
        $instance = self::getInstance();

        $instance->$param  = $value;
    }

    /**
     * TODO
     *
     * @param string $param
     * @return mixed
     */
    public function __get($param)
    {
        return $this->repository->$param;
    }

    /**
     * TODO
     *
     * @param string $param
     * @param mixed $value
     */
    public function __set($param, $value)
    {
        $this->repository->$param = $value;
    }

    /**
     * TODO
     *
     * @param string $param
     * @return bool
     */
    public function __isset($param)
    {
        return isset($this->repository->$param);
    }
}
