<?php
require_once 'lib/lessc.inc.php';

class Ajde_Document_Processor_Css_Less extends Ajde_Object_Static implements Ajde_Document_Processor
{
	// Not implemented
	public static function preProcess(Ajde_Layout $layout) {}
	public static function postCompress(Ajde_Resource_Local_Compressor $compressor) {}
	
	public static function preCompress(Ajde_Resource_Local_Compressor $compressor)
	{
		// Check type as this function can be called from Ajde_Event binding to
		// abstract Ajde_Resource_Local_Compressor class in Ajde_Resource_Local_Compressor::saveCache()
		if ($compressor->getType() == Ajde_Resource::TYPE_STYLESHEET) {
			$compressor->setContents(self::lessifyCss($compressor->getContents()));
		}
	}
		
	public static function postProcess(Ajde_Layout $layout)
	{
		$layout->setContents(self::lessifyCss($layout->getContents()));
	}

	public static function lessifyCss($css)
	{
		if (substr_count($css, '/*#!less*/') === 0) {
			return $css;
		}
		$less = new lessc();
		try {
			$lesser = $less->parse($css);
		} catch(Exception $e) {
			Ajde_Exception_Log::logException($e);
			return $css;
		}
		return $lesser;
	}
	
}