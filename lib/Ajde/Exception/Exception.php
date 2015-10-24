<?php

class Ajde_Exception extends Exception
{
	protected $_traceOnOutput = true;
	
	public function __construct($message = null, $code = 0, $traceOnOutput = true)
    {
		$this->_traceOnOutput = $traceOnOutput;
        parent::__construct($message, $code);
    }
	
	public function traceOnOutput()
	{
		return $this->_traceOnOutput;
	}
	
	public function process()
	{
		return Ajde_Exception_Handler::handler($this);
	}
	
}