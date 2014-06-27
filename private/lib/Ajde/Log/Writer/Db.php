<?php

class Ajde_Log_Writer_Db extends Ajde_Log_Writer_Abstract
{
    public static function _($message, $channel = Ajde_Log::CHANNEL_INFO, $level = Ajde_Log::LEVEL_INFORMATIONAL, $description = '', $code = '', $trace = '')
    {
        Ajde_Model::register('admin');

		$log = new LogModel();
        $log->populate(array(
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
        ));
        $log->insert();
	}
}