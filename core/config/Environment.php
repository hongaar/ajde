<?php

class Environment extends Ajde_Object_Singleton
{
    const ENV_DEFAULT = 'production';

    /**
     * @var string
     */
    private $environment;

    /**
     * Environment <-> network map.
     *
     * @author http://en.wikipedia.org/wiki/Private_network
     * @var array
     */
    public static $networks = [
        'dev' => [
            '/::1/',
            '/127\.0\.0\.1/',
            '/10\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/',
            '/172\.[1-3][0-9]\.[0-9]{1,3}\.[0-9]{1,3}/',
            '/192\.168\.[0-9]{1,3}\.[0-9]{1,3}/',
        ]
    ];

    /**
     * TODO
     */
    public function __construct()
    {
        $this->environment = $this->autoDetect();
    }

    /**
     * TODO
     *
     * @return Environment
     */
    public static function getInstance()
    {
        static $instance;
        return $instance === null ? $instance = new self : $instance;
    }

    /**
     * TODO
     *
     * @return string
     */
    public static function current()
    {
        $instance = self::getInstance();
        return $instance->environment();
    }

    /**
     * TODO
     *
     * @return string
     */
    public function environment()
    {
        return $this->environment;
    }

    /**
     * TODO
     *
     * @return string
     */
    private function autoDetect()
    {
        if (file_exists(LOCAL_ROOT . '.env')) {
            return trim(file_get_contents(LOCAL_ROOT . '.env'));
        }

        foreach (self::$networks as $environment => $ips) {
            foreach ($ips as $pattern) {
                if (preg_match($pattern, $_SERVER['SERVER_ADDR'])) {
                    return $environment;
                }
            }
        }

        return self::ENV_DEFAULT;
    }
}
