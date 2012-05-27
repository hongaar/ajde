<?php

class Ajde_Core_Documentation
{
	const DOCUMENTATION_URL = 'https://code.google.com/p/ajde/wiki/Exception%s';

	public static function getUrl($code)
	{
		return sprintf(self::DOCUMENTATION_URL, $code);
	}
}