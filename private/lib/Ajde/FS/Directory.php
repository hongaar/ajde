<?php

class Ajde_FS_Directory extends Ajde_Object_Static
{
	public static function delete($dir, $truncate = false)
	{
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir($dir . DIRECTORY_SEPARATOR . $file)) ?
				self::delete($dir . DIRECTORY_SEPARATOR . $file) : unlink($dir . DIRECTORY_SEPARATOR . $file);
		}
		if ($truncate === false) {
			rmdir($dir);
		}
	}
	
	public static function truncate($dir)
	{
		self::delete($dir, true);
	}
	
	public static function copy($source, $dest)
	{
		// recursive function to copy
		// all subdirectories and contents:
		if (is_dir($source)) {
			$dir_handle = opendir($source);
			$sourcefolder = basename($source);
			mkdir($dest . DIRECTORY_SEPARATOR . $sourcefolder);
			while ($file = readdir($dir_handle)) {
				if ($file != "." && $file != "..") {
					if (is_dir($source . DIRECTORY_SEPARATOR . $file)) {
						self::copy($source . DIRECTORY_SEPARATOR . $file, $dest . DIRECTORY_SEPARATOR . $sourcefolder);
					} else {
						copy($source . DIRECTORY_SEPARATOR . $file, $dest . DIRECTORY_SEPARATOR . $file);
					}
				}
			}
			closedir($dir_handle);
		} else {
			// can also handle simple copy commands
			copy($source, $dest);
		}
	}
}