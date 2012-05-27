<?php

class Ajde_Exception_Log extends Ajde_Object_Static
{
	static public function logException(Exception $exception)
	{
		$trace = strip_tags( Ajde_Exception_Handler::trace($exception, Ajde_Exception_Handler::EXCEPTION_TRACE_LOG) );
		Ajde_Log::log($trace);
	}
}