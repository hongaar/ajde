<?php

abstract class Ajde_Shop_Shipping_Method extends Ajde_Object_Standard
{
	public function setTransaction(Ajde_Shop_Transaction $transaction)
	{
		parent::setTransaction($transaction);
	}

	/**
	 *
	 * @return Ajde_Shop_Transaction
	 */
	protected function getTransaction()
	{
		return parent::getTransaction();
	}

	abstract public function getDescription();
	abstract public function getTotal();
	
	protected function _format($value)
	{
		return money_format('%!i', $value);
	}
	
	public function getFormattedTotal()
	{
		return Config::get('currency') . ' ' . $this->_format($this->getTotal());
	}
}