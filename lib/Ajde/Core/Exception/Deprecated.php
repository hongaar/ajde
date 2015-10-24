<?php 

class Ajde_Core_Exception_Deprecated extends Ajde_Exception
{
	public function __construct($message = null, $code = null)
	{
		$message = $message ? $message : 'Call to deprecated function or method';
		parent::__construct($message, $code);
	}
}