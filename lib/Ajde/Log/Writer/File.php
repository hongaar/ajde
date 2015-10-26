<?php

class Ajde_Log_Writer_File extends Ajde_Log_Writer_Abstract
{
    private static function _getFilename()
    {
        return LOG_DIR . date("Ymd") . '.log';
    }

    public static function _(
        $message,
        $channel = Ajde_Log::CHANNEL_INFO,
        $level = Ajde_Log::LEVEL_INFORMATIONAL,
        $description = '',
        $code = '',
        $trace = ''
    ) {
        $filename = self::_getFilename();
        if (!is_writable(LOG_DIR)) {
            // TODO, throw error here??
            throw new Ajde_Exception(sprintf("Directory %s is not writable", LOG_DIR), 90014);
        }
        $fh = fopen($filename, 'a');
        if (!$fh) {
            /*
             * Don't throw an exception here, since this function is generally
             * called from an error handler
             */
            return false;
        }
        fwrite($fh, PHP_EOL . PHP_EOL . date("H:i:sP") . ":" . PHP_EOL);
        fwrite($fh, $message . PHP_EOL);
        fwrite($fh, "\tChannel: $channel" . PHP_EOL);
        fwrite($fh, "\tLevel: $level" . PHP_EOL);
        fwrite($fh, "\tDescription: $description" . PHP_EOL);
        fwrite($fh, "\tCode: $code" . PHP_EOL);
        fwrite($fh, "\tTrace: $trace" . PHP_EOL);
        fwrite($fh, "\tRequest: " . self::getRequest() . PHP_EOL);
        fwrite($fh, "\tUser agent: " . self::getUserAgent() . PHP_EOL);
        fwrite($fh, "\tReferer: " . self::getReferer() . PHP_EOL);
        fwrite($fh, "\tIP: " . self::getIP() . PHP_EOL);
        fclose($fh);

        return true;
    }
}
