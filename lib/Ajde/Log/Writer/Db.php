<?php

class Ajde_Log_Writer_Db extends Ajde_Log_Writer_Abstract
{
    public static function _(
        $message,
        $channel = Ajde_Log::CHANNEL_INFO,
        $level = Ajde_Log::LEVEL_INFORMATIONAL,
        $description = '',
        $code = '',
        $trace = ''
    ) {
        // don't use db writer on db error
        if (substr_count($message, 'SQLSTATE')) {
            return false;
        }

        $log = new LogModel();
        $log->populate([
            'message' => $message,
            'channel' => $channel,
            'level' => $level,
            'description' => $description,
            'code' => $code,
            'trace' => $trace,
            'request' => self::getRequest(),
            'user_agent' => self::getUserAgent(),
            'referer' => self::getReferer(),
            'ip' => self::getIP()
        ]);

        return $log->insert();
    }
}
