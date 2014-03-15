<?php

class Ajde_Resource_JsLibrary extends Ajde_Object_Static
{
	public static $base = '//ajax.googleapis.com/ajax/libs/';
	
	/*
	 * @see http://code.google.com/apis/libraries/devguide.html#Libraries
	 */
	public static $libraries = array(
		'jquery' 		=> 'jquery.min.js',
		'jqueryui' 		=> 'jquery-ui.min.js',
		'prototype' 	=> 'prototype.js',
		'scriptaculous' => 'scriptaculous.js',
		'mootools' 		=> 'mootools-yui-compressed.js',
		'dojo' 			=> 'dojo/dojo.xd.js',
		'swfobject' 	=> 'swfobject.js',
		'yui' 			=> 'build/yuiloader/yuiloader-min.js',
		'ext-core' 		=> 'ext-core.js',
		'chrome-frame' 	=> 'CFInstall.min.js',
		'webfont' 		=> 'webfont.js'
	);

	public static function getUrl($name, $version)
	{
		if (array_key_exists($name, $libraries = self::$libraries))
		{
			$url = self::$base . $name . '/' . $version . '/' . $libraries[$name];
			return $url;
		}
		else
		{
			throw new Ajde_Exception(sprintf('JavaScript library %s not
				available', $name), 90013);
		}
	}
}