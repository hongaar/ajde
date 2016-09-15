<?php

class Ajde_Exception_Log extends Ajde_Object_Static
{
    /**
     * @param Throwable $exception
     */
    static public function logException($exception)
    {
        $type    = Ajde_Exception_Handler::getTypeDescription($exception);
        $level   = Ajde_Exception_Handler::getExceptionLevelMap($exception);
        $channel = Ajde_Exception_Handler::getExceptionChannelMap($exception);
        $trace   = strip_tags(Ajde_Exception_Handler::trace($exception, Ajde_Exception_Handler::EXCEPTION_TRACE_ONLY));

        Ajde_Log::_($exception->getMessage(), $channel, $level, $type,
            sprintf("%s on line %s", $exception->getFile(), $exception->getLine()), $trace);
    }
}
