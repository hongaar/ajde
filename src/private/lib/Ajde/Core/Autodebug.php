<?php

class Ajde_Core_Autodebug extends Ajde_Object_Singleton
{	
	public static function getInstance()
	{
		static $instance;
		return $instance === null ? $instance = new self : $instance;
	}
	 
	static public function __bootstrap()
	{
		Ajde_Model::register('user');
		if ( ($user = Ajde_User::getLoggedIn()) && $user->getDebug()) {
			$config = Config::getInstance();
			$config->debug = true;
		}
		return true;
	}
}