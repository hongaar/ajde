<?php

class Ajde_Acl extends Ajde_Model
{
	public static $log = array(); 
	public static $access = null;
	
	protected $_autoloadParents = false;
		
    public static function getLog() {  
        return self::$log;  
    }	
	
	public function addPermission($user = 0, $usergroup = 0, $module = '*', $permission = '*')
	{
		// TODO: implement
	}
}
