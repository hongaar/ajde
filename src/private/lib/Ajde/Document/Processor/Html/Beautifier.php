<?php

class Ajde_Document_Processor_Html_Beautifier extends Ajde_Object_Static implements Ajde_Document_Processor
{
	// Not implemented
	public static function preProcess(Ajde_Layout $layout) {}
	public static function preCompress(Ajde_Resource_Local_Compressor $compressor) {}
	public static function postCompress(Ajde_Resource_Local_Compressor $compressor) {}
		
	public static function postProcess(Ajde_Layout $layout)
	{
		$layout->setContents(self::beautifyHtml($layout->getContents()));
	}

	public static function beautifyHtml($html,
		// @see http://tidy.sourceforge.net/docs/quickref.html
		$config = array(
			"output-xhtml" 	=> true,
			"char-encoding"	=> "utf8",
			"indent" 		=> true,
			"indent-spaces"	=> 4,
			"wrap"			=> 0
		)
	)
	{
		if (!Ajde_Core_Autoloader::exists('Tidy')) {
			throw new Ajde_Exception('Class Tidy not found', 90023);
		}
		$tidy = new Tidy();
		// tidy does not produce valid utf8 when the encoding is specified in the config
		// so we provide a third parameter, 'utf8' to fix this
		// @see http://bugs.php.net/bug.php?id=35647
		return $tidy->repairString($html, $config, 'utf8');
	}
	
}