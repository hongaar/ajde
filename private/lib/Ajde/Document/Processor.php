<?php

interface Ajde_Document_Processor
{
	public static function preProcess(Ajde_Layout $layout);	
	public static function postProcess(Ajde_Layout $layout);
	
	public static function preCompress(Ajde_Resource_Local_Compressor $compressor);
	public static function postCompress(Ajde_Resource_Local_Compressor $compressor);
}