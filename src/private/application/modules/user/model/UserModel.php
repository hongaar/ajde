<?php

class UserModel extends Ajde_User
{
	public $usernameField = 'username';
	public $passwordField = 'password';
	
	public $defaultUserGroup = self::USERGROUP_USERS;
	
	public function __construct() {
		parent::__construct();
		$this->registerEvents();
		$this->setEncryptedFields(array(
			'email', 'fullname', 'address', 'zipcode', 'city', 'region', 'country'
		));
	}
	
	public function __wakeup()
	{
		parent::__wakeup();
		$this->registerEvents();
	}
	
	public function registerEvents()
	{
		if (!Ajde_Event::has($this, 'afterCrudLoaded', 'parseForCrud')) {
			Ajde_Event::register($this, 'afterCrudLoaded', 'parseForCrud');
			Ajde_Event::register($this, 'beforeCrudSave', 'prepareCrudSave');
		}
	}
	
	public function afterSave()
	{
		if ($this->getPK() == $this->getLoggedIn()->getPK()) {
			$this->login();
		}
	}
	
	public function emailLink()
	{
		return '<a href="mailto:' . _e($this->getEmail()) . '">' . _e($this->getEmail()) . '</a>';
	}
	
	public function parseForCrud(Ajde_Crud $crud)
	{
		$this->set($this->passwordField, '');
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