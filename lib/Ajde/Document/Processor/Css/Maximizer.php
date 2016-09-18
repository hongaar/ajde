<?php

require_once 'lib/maximizer/CSS3Maximizer.php';
require_once 'lib/maximizer/inc/ColorSpace.php';

class Ajde_Document_Processor_Css_Maximizer extends Ajde_Object_Static implements Ajde_Document_Processor
{
    // Not implemented
    public static function preProcess(Ajde_Layout $layout)
    {
    }

    public static function postCompress(Ajde_Resource_Local_Compressor $compressor)
    {
    }

    public static function preCompress(Ajde_Resource_Local_Compressor $compressor)
    {
        // Check type as this function can be called from Ajde_Event binding to
        // abstract Ajde_Resource_Local_Compressor class in Ajde_Resource_Local_Compressor::saveCache()
        if ($compressor->getType() == Ajde_Resource::TYPE_STYLESHEET) {
            $compressor->setContents(self::clean($compressor->getContents()));
        }
    }

    public static function postProcess(Ajde_Layout $layout)
    {
        $layout->setContents(self::clean($layout->getContents()));
    }

    public static function clean($css)
    {
        $maximizer = new CSS3Maximizer();
        // try {
        $maximized = $maximizer->clean(['css' => $css]);
        // } catch(Exception $e) {
        // 	Ajde_Exception_Log::logException($e);
        // 	return $css;
        // }
        return $maximized;
    }
}
