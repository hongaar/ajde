<?php

class Ajde_FS_Find extends Ajde_Object_Static
{
	public static function findFile($dir, $pattern)
	{
		$search = Config::get("local_root") . '/' . $dir . $pattern;
		$result = glob($search);
		if ($result === false) {
			return false;
		}
		foreach (glob($search) as $filename) {
			return $filename;
		}
		return false;
	}
	
	public static function findFiles($dir, $pattern, $flags = 0)
	{
		$search = Config::get("local_root") . '/' . $dir . $pattern;
		$return = array();
		foreach (glob($search, $flags) as $filename) {
			$return[] = $filename;
		}
		return $return;
	}
}