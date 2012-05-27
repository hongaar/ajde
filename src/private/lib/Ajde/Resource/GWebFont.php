<?php

class Ajde_Resource_GWebFont extends Ajde_Object_Static
{
	public static $base = 'http://fonts.googleapis.com/css?';
	
	public static function getUrl($family, $weight = array(400), $subset = array('latin'))
	{
		if (is_array($weight)) {
			$weight = implode(',', $weight);
		}
		if (is_array($subset)) {
			$subset = implode(',', $subset);
		}
		$qs = array(
			'family' => $family . ':' . $weight,
			'subset' => $subset			
		);
		return self::$base . http_build_query($qs);
	}
}