<?php

class Ajde_Log extends Ajde_Object_Static
{
	private static function _getFilename()
	{
		return LOG_DIR . date("Ymd") . '.log';
	}
	
	public static function log($string)
	{
		$filename = self::_getFilename();
		if (!is_writable(LOG_DIR))
		{
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
		fwrite($fh, $string);
		fwrite($fh, PHP_EOL . "Debug info - user agent: " . issetor($_SERVER["HTTP_USER_AGENT"]) . " - referer: " . issetor($_SERVER["HTTP_REFERER"]));
		fclose($fh);
	}
}