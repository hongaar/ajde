<?php

class UserModel extends Ajde_User
{
	public $usernameField = 'username';
	public $passwordField = 'password';
	
	public $defaultUserGroup = self::USERGROUP_USERS;
	
	public $decryptedFields = array(
		'email', 'fullname', 'address', 'zipcode', 'city', 'region', 'country'
	);
	
	public function __construct() {
		Ajde_Event::register($this, 'afterCrudLoaded', 'parseForCrud');
		Ajde_Event::register($this, 'beforeCrudSave', 'prepareCrudSave');
		parent::__construct();
	}
	
	public function beforeSave()
	{
		foreach($this->decryptedFields as $field) {
			if ($this->has($field)) {
				$this->encrypt($field);
			}
		}
	}
	
	public function emailLink()
	{
		return '<a href="mailto:' . _e($this->getEmail()) . '">' . _e($this->getEmail()) . '</a>';
	}
	
	public function parseForCrud(Ajde_Crud $crud)
	{
		$this->set($this->passwordField, '');

		foreach($this->decryptedFields as $field) {
			if ($this->has($field)) {
				$this->decrypt($field);
			}
		}
	}
	
	public function getEmail()
	{
		return $this->decrypt('email');
	}
	
	public function getFullname()
	{
		return $this->decrypt('fullname');
	}
	
	public function prepareCrudSave(Ajde_Controller $controller, Ajde_Crud $crud)
	{
		if ($this->hasNotEmpty($this->passwordField)) {
			$password = $this->get($this->passwordField);
			$hash = $this->createHash($password);                
			$this->set($this->passwordField, $hash);
		}
		
		if ($this->hasEmpty('secret')) {
			$this->set('secret', $this->generateSecret());
		}
	}
}