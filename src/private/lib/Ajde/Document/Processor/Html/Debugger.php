<?php

class Ajde_Document_Processor_Html_Debugger extends Ajde_Object_Static implements Ajde_Document_Processor
{
	// Not implemented
	public static function preCompress(Ajde_Resource_Local_Compressor $compressor) {}
	public static function postCompress(Ajde_Resource_Local_Compressor $compressor) {}
	
	public static function preProcess(Ajde_Layout $layout) {
		// invoke here to add resources
		Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/debugger:view.html'))->invoke();
	}
	
	public static function postProcess(Ajde_Layout $layout) {
		$debugger = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/debugger:view.html'))->invoke();
		$layout->setContents($layout->getContents() . $debugger);		
	}	
}