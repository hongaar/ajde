<?php

class Ajde_Log_Writer_Php extends Ajde_Log_Writer_Abstract
{
    public static function _(
        $message,
        $channel = Ajde_Log::CHANNEL_INFO,
        $level = Ajde_Log::LEVEL_INFORMATIONAL,
        $description = '',
        $code = '',
        $trace = ''
    ) {
        error_log($message.PHP_EOL.$trace);

        return false; // Passthrough
    }
}
