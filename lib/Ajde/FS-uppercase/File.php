<?php

class Ajde_Fs_File extends Ajde_Object_Static
{
    const TYPE_EXTENSION = 'ext';
    const TYPE_MIMETYPE = 'mime';

    public static function getMimeType($filename)
    {
        $realpath = realpath($filename);

        if ($realpath
            && function_exists('finfo_file')
            && function_exists('finfo_open')
            && defined('FILEINFO_MIME_TYPE')
        ) {

            // Use the Fileinfo PECL extension (PHP 5.3+)
            return finfo_file(finfo_open(FILEINFO_MIME_TYPE), $realpath);
        }
        if (function_exists('mime_content_type')) {

            // Deprecated in PHP 5.3
            return mime_content_type($realpath);
        }

        return false;
    }

    public static function getExtensionFromMime($mimeType)
    {
        $mimes = self::_mimetypes();

        return array_search($mimeType, $mimes);
    }

    public static function getMimeFromExtension($extension)
    {
        $mimes = self::_mimetypes();

        return isset($mimes[strtolower($extension)]) ? $mimes[strtolower($extension)] : false;
    }

    private static function _mimetypes()
    {
        return include "mimetypes.php";
    }
}
