<?php

class Ajde_Resource_GWebFont extends Ajde_Object_Static
{
    public static $base = '//fonts.googleapis.com/css?';

    public static function getUrl($family, $weight = [400], $subset = ['latin'])
    {
        if (is_array($weight)) {
            $weight = implode(',', $weight);
        }
        if (is_array($subset)) {
            $subset = implode(',', $subset);
        }
        $qs = [
            'family' => $family.':'.$weight,
            'subset' => $subset,
        ];

        return self::$base.http_build_query($qs);
    }
}
