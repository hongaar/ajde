<?php

class Ajde_Fs_Directory extends Ajde_Object_Static
{
    public static function delete($dir, $truncate = false)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
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

    public static function copy($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                    self::copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                } else {
                    copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir);
    }
}
