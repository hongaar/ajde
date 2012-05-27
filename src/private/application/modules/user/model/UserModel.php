<?php

class UserModel extends Ajde_User
{
	public $usernameField = 'username';
	public $passwordField = 'password';
	
	public $defaultUserGroup = self::USERGROUP_USERS;
}