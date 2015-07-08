<?php

class Ajde_Log extends Ajde_Object_Static
{
    const CHANNEL_EXCEPTION     = 'Exception';
    const CHANNEL_ERROR         = 'Error';
    const CHANNEL_ROUTING       = 'Routing';
    const CHANNEL_SECURITY      = 'Security';
    const CHANNEL_INFO          = 'Info';
    const CHANNEL_APPLICATION   = 'Application';

    const LEVEL_EMERGENCY       = '1:Emergency';
    const LEVEL_ALERT           = '2:Alert';
    const LEVEL_CRITICAL        = '3:Critical';
    const LEVEL_ERROR           = '4:Error';
    const LEVEL_WARNING         = '5:Warning';
    const LEVEL_NOTICE          = '6:Notice';
    const LEVEL_INFORMATIONAL   = '7:Informational';
    const LEVEL_DEBUG           = '8:Debug';

	private static function getWriters()
	{
        $writerArray = Config::get('logWriter');
        $getWriters = array();
        foreach($writerArray as $writer) {
            $getWriters[] = 'Ajde_Log_Writer_' . ucfirst($writer);
        }
        return $getWriters;
	}

    public static function _($message, $channel = self::CHANNEL_INFO, $level = self::LEVEL_INFORMATIONAL, $description = '', $code = '', $trace = '')
    {
        foreach(self::getWriters() as $writer)
        {
            try {
                $result = @call_user_func_array($writer . '::_', array($message, $channel, $level, $description, $code, $trace));
                if ($result) {
                    break;
                }
            } catch (Exception $e) {}
        }
    }

    public static function d($message, $description = '')
    {
        self::_($message, self::CHANNEL_INFO, self::LEVEL_INFORMATIONAL, $description);
    }
	
	public static function log($string)
	{
		self::_($string);
	}
}