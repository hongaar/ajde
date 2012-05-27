<?php

abstract class Ajde_Object
{
	const OBJECT_PATTERN_UNDEFINED		= 0;
	const OBJECT_PATTERN_STANDARD		= 1;
	const OBJECT_PATTERN_SINGLETON		= 2;
	const OBJECT_PATTERN_STATIC			= 3;

	protected static $__pattern;

	/**
	 * We would want to make this non-abstract, but since get_called_class() is
	 * not available in PHP < 5.3.0 we implement this in all subclasses
	 *
	 * It would look like this:
	 * 
	 * public static function __getPattern()
	 * {
	 *  $caller = get_called_class();
	 * 	return $caller::$__pattern;
	 * }
	 */
	public static function __getPattern()
	{
		// Implement in subclasses
		// @see http://stackoverflow.com/questions/999066/why-does-php-5-2-disallow-abstract-static-class-methods
	}
}