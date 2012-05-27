<?php

class Ajde_Shop_Cart_Merge extends Ajde_Object_Singleton
{	
	public static function getInstance()
	{
		static $instance;
		return $instance === null ? $instance = new self : $instance;
	}
	 
	static public function __bootstrap()
	{
		self::mergeClientToUser();
		return true;
	}
	
	static public function mergeClientToUser()
	{
		Ajde_Model::register('user');
		Ajde_Model::register('shop');
		
		if ($user = Ajde_User::getLoggedIn()) {
			
			// Do we have a saved client cart?
			$clientCart = new CartModel();			
			if ($clientCart->loadByClient()) {
				
				// Do we have a saved cart for logged in user?
				$userCart = new CartModel();				
				if ($userCart->loadByUser($user) === false) {
					$userCart->user = $user->getPK();
					$userCart->insert();
				}
				
				if ($userCart->hasItems()) {
					// Set alert message
					Ajde_Session_Flash::alert(__('We updated your shopping cart now you\'re logged in'));
				}
				
				// Merge items
				foreach($clientCart->getItems() as $item) {				
					/* @var $item Ajde_Shop_Cart_Item */
					$userCart->addItem($item->getEntity(), null, $item->getQty());
				}
				
				// And delete client
				$clientCart->delete();				
			}
		}
	}
	
	static public function mergeUserToClient()
	{
		Ajde_Model::register('user');
		Ajde_Model::register('shop');
		
		if ($user = Ajde_User::getLoggedIn()) {	
			
			// Do we have a saved cart for logged in user?
			$userCart = new CartModel();			
			if ($userCart->loadByUser($user)) {				
							
				// Do we have a saved cart for client?				
				$clientCart = new CartModel();
				if ($clientCart->loadByClient() === false) {
					$clientCart->client = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
					$clientCart->insert();
				}				
				
				foreach($userCart->getItems() as $item) {
					/* @var $item Ajde_Shop_Cart_Item */
					$clientCart->addItem($item->getEntity(), null, $item->getQty());
				}
				
				$userCart->delete();
			}
		}
	}
}