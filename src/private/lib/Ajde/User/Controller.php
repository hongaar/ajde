<?php 

abstract class Ajde_User_Controller extends Ajde_Controller
{		
	protected $_user = null;
	protected $_registerUserModels = array('user');
	
	protected $_allowedActions = array(
		'logon',
	);
	protected $_logonRoute = 'user/logon/html';
	
	public function beforeInvoke()
	{
		if ($this->hasAccess()) {
			return true;
		} else {
			Ajde::app()->getRequest()->set('message', __('Please log on to view this page'));
			Ajde::app()->getResponse()->dieOnCode(Ajde_Http_Response::RESPONSE_TYPE_UNAUTHORIZED);
		}
	}
	
	protected function hasAccess()
	{			
		// TODO: possible undesired behaviour when called by Ajde_Acl_Controller,
		// when that controller is invoked with a allowed action like 'logon'
		return (in_array($this->getAction(), $this->_allowedActions) || $this->getLoggedInUser() !== false);
	}
	
	protected function addTimeoutWarning()
	{
		// Add timeout warning to layout
		if ($this->getLoggedInUser() !== false) {
			Ajde_Event::register('Ajde_Layout', 'beforeGetContents', 'requireTimeoutWarning');
		}
	}
	
	/**
	 *
	 * @return UserModel
	 */
	protected function getLoggedInUser()
	{
		if (!isset($this->_user)) {
			foreach($this->_registerUserModels as $model) {
				Ajde_Model::register($model);
			}	
			$user = new UserModel();
			$this->_user = $user->getLoggedIn();
		}
		return $this->_user;
	}	
}