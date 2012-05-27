<?php

abstract class Ajde_Shop_Transaction extends Ajde_Model
{
	protected $_shippingModel;
	
	// Payment
	
	public static function getProviders()
	{
		return self::_getProviders();
	}
	
	private static function _getProviders()
	{
		$return = array();
		$providers = Config::get('transactionProviders');	
		foreach($providers as $provider) {
			$return[$provider] = Ajde_Shop_Transaction_Provider::getProvider($provider);
		}
		return $return;
	}
	
	/**
	 *
	 * @return Ajde_Shop_Transaction_Provider
	 */
	public function getProvider()
	{
		return Ajde_Shop_Transaction_Provider::getProvider($this->payment_provider, $this);
	}
	
	// Update IP / Secret
	
	public function beforeInsert()
	{
		$this->secret = $this->generateSecret();
		$this->ip = $_SERVER["REMOTE_ADDR"];
	}
	
	public function generateSecret($length = 255)
	{
		return substr(sha1(mt_rand()), 0, $length);
	}
	
	// Shipping
	
	/**
	 *
	 * @return Ajde_Shop_Shipping
	 */
	public function getShipping()
	{
		return $this->_getShippingModel();
	}	
	
	private function _getShippingModel()
	{
		$shippingModelName = $this->_shippingModel;
		return new $shippingModelName($this);
	}
	
	// Helpers
	
	protected function _format($value)
	{
		return money_format('%!i', $value);
	}
	
	public function getTotal()
	{
		return $this->payment_amount;
	}
	
	public function getFormattedTotal()
	{
		return Config::get('currency') . ' ' . $this->_format($this->getTotal());
	}
	
}