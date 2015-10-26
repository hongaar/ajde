<?php

abstract class Ajde_Log_Writer_Abstract
{
    abstract public static function _(
        $message,
        $channel = self::CHANNEL_INFO,
        $level = self::LEVEL_INFORMATIONAL,
        $description = '',
        $code = '',
        $trace = ''
    );

    public static function getIP()
    {
        return issetor($_SERVER['REMOTE_ADDR']);
    }

    public static function getReferer()
    {
        return issetor($_SERVER['HTTP_REFERER']);
    }

    public static function getUserAgent()
    {
        return issetor($_SERVER['HTTP_USER_AGENT']);
    }

    public static function getRequest()
    {
        return issetor($_SERVER["REQUEST_URI"]);
    }

}
