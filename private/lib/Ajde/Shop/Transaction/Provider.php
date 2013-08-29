<?php

interface Ajde_Shop_Transaction_Provider_Interface
{
	public function getName();	
	public function getLogo();
	public function usePostProxy();
	public function getRedirectUrl();
	public function getRedirectParams();
	public function updatePayment();
}

abstract class Ajde_Shop_Transaction_Provider extends Ajde_Object_Standard
implements Ajde_Shop_Transaction_Provider_Interface
{
	/**
	 *
	 * @param string $name
	 * @param Ajde_Shop_Transaction $transaction
	 * @return Ajde_Shop_Transaction_Provider
	 * @throws Ajde_Exception 
	 */
	public static function getProvider($name, $transaction = null)
	{
		$providerClass = 'Ajde_Shop_Transaction_Provider_' .ucfirst($name);
		if (!Ajde_Core_Autoloader::exists($providerClass)) {
			// TODO:
			throw new Ajde_Exception('Payment provider ' . $name . ' not found');
		}
		$obj = new $providerClass();
		if ($transaction) {
			$obj->setTransaction($transaction);
		}
		return $obj;
	}
	
	public function setTransaction($transaction) 
	{
		parent::setTransaction($transaction);
	}
	
	/**
	 *
	 * @return Ajde_Shop_Transaction
	 */
	public function getTransaction()
	{
		return parent::getTransaction();
	}
	
	public function isSandbox()
	{
		return Config::get('shopSandboxPayment');
	}
	
	protected function ping($url, $port = 80, $timeout = 6)
	{
		$host = parse_url($url, PHP_URL_HOST);
		$fsock = fsockopen($host, $port, $errno, $errstr, $timeout);
		if (!$fsock) {
			Ajde_Log::log('Ping for ' . $host . ':' . $port . ' (timeout=' . $timeout . ') failed');
			return false;
		} else {
			return true;
		}
	}
}