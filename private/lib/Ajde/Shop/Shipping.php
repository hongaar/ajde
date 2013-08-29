<?php

abstract class Ajde_Shop_Shipping extends Ajde_Object_Standard
{
	protected $_methods = array();
	
	public function __construct(Ajde_Shop_Transaction $transaction) {
		$this->setTransaction($transaction);
	}
	
	public function addMethod(Ajde_Shop_Shipping_Method $method)
	{
		$method->setName(strtolower(str_replace('Model', '', get_class($method))));
		$method->setTransaction($this->getTransaction());
		$this->_methods[] = $method;
	}
	
	public function getMethods()
	{
		return $this->_methods;
	}
	
	public function getFirstMethod()
	{
		if (count($this->_methods) > 0) {
			return $this->_methods[0];
		}
		return false;
	}
	
	public function isAvailable($name)
	{
		foreach($this->getMethods() as $method)	{
			if ($method->getName() == $name) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 *
	 * @param string $name
	 * @return Ajde_Shop_Shipping_Method
	 */
	public function getMethod($name)
	{
		foreach($this->getMethods() as $method)	{
			if ($method->getName() == $name) {
				return $method;
			}
		}
		return false;
	}
}