<?php 

abstract class Ajde_Acl_Controller extends Ajde_User_Controller
{		
	protected $_aclCollection = null;	
	protected $_registerAclModels = array('acl');
	
	protected $_allowedAction = array();
	
	/* ACL sets this to true or false to grant/prevent access in beforeInvoke() */
	private $_hasAccess;
	
	public function beforeInvoke()
	{
		foreach($this->_registerAclModels as $model) {
			Ajde_Model::register($model);
		}
		if (!in_array($this->getAction(), $this->_allowedActions) && $this->hasAccess() === false) {
			Ajde::app()->getRequest()->set('message', __('Please login to continue / No access'));
			Ajde::app()->getResponse()->dieOnCode(Ajde_Http_Response::RESPONSE_TYPE_UNAUTHORIZED);
		} else {
			return true;
		}
	}
	
	protected function getOwnerId()
	{
		return false;
	}
	
	protected function getAclParam()
	{
		return parent::getAclParam();
	}
	
	protected function setAclParam($param)
	{
		parent::setAclParam($param);
	}
	
	/**
	 * @return AclCollection
	 */
	protected function getAclCollection()
	{
		if (!isset($this->_aclCollection)) {
			$this->_aclCollection = new AclCollection();
		}
		return $this->_aclCollection;
	}
	
	public function validateAccess()
	{
		$user = parent::getLoggedInUser();
		if ($user !== false) {
			$uid = $user->getPK();
			$usergroup = $user->getUsergroup();	
		} else {
			$uid = -1;
			$usergroup = -1;
		}		
		$module = $this->getModule();
		$action = $this->getAction();
		$param = $this->hasAclParam() ? $this->getAclParam() : '';
		$controller = $this->getRoute()->hasController() ? $this->getRoute()->getController() : '';
		$extra = $controller . ($controller && $param ? ':' : '') . $param;
		return $this->validateAclFor($uid, $usergroup, $module, $action, $extra);
	}
	
	private function validateAclFor($uid, $usergroup, $module, $action, $extra)
	{
		/**
		 * TODO: Nasty code...
		 * TODO: refactor to Ajde_Acl
		 */
		
		/**
		 * Allright, this is how things go down here:
		 * We want to check for at least one allowed or owner record in this direction:
		 * 		 
		 * 1. Wildcard usergroup AND module/action
		 * 2. Wildcard user AND module/action
		 * 3. Specific usergroup AND module/action
		 * 4. Specific user AND module/action
		 * 5. Public AND module/action
		 * 
		 * Module/action goes down in this order:
		 * 
		 * A1. Wildcard module AND wildcard action
		 * A2. Wildcard module AND wildcard action (with extra)
		 * B1. Wildcard module AND specific action
		 * B2. Wildcard module AND specific action (with extra)
		 * C1. Specific module AND wildcard action
		 * C2. Specific module AND wildcard action (with extra)
		 * D1. Specific module AND specific action
		 * D2. Specific module AND specific action (with extra)
		 * 
		 * This makes for 20 checks.
		 * 
		 * If a denied record is found and no allowed or owner record is present
		 * further down, deny access.
		 */
		
		$access = false;
		
		$moduleAction = array(
			"A1" => array(
				'module' => '*',
				'action' => '*',
				'extra' => '*'
			),
			"A2" => array(
				'module' => '*',
				'action' => '*',
				'extra' => $extra
			),
			"B1" => array(
				'module' => '*',
				'action' => $action,
				'extra' => '*'
			),
			"B2" => array(
				'module' => '*',
				'action' => $action,
				'extra' => $extra
			),
			"C1" => array(
				'module' => $module,
				'action' => '*',
				'extra' => '*'
			),
			"C2" => array(
				'module' => $module,
				'action' => '*',
				'extra' => $extra
			),
			"D1" => array(
				'module' => $module,
				'action' => $action,
				'extra' => '*'
			),
			"D2" => array(
				'module' => $module,
				'action' => $action,
				'extra' => $extra
			)
		);
		
		$userGroup = array(			
			1 => array('usergroup',	null),
			2 => array('user',		null),
			3 => array('usergroup',	$usergroup),
			4 => array('user',		$uid),
			5 => array('public',	null)
		);
		
		/**
		 * Allright, let's prepare the SQL!
		 */
		
		$rules = $this->getAclCollection();
		$rules->reset();
		
//		$moduleActionWhereGroup = new Ajde_Filter_WhereGroup(Ajde_Query::OP_AND);
//		foreach($moduleAction as $moduleActionPart) {
//			$group = new Ajde_Filter_WhereGroup(Ajde_Query::OP_OR);
//			foreach($moduleActionPart as $key => $value) {
//				$group->addFilter(new Ajde_Filter_Where($key, Ajde_Filter::FILTER_EQUALS, $value, Ajde_Query::OP_AND));
//			}
//			$moduleActionWhereGroup->addFilter($group);
//		}
//				
//		foreach($userGroup as $userGroupPart) {
//			$group = new Ajde_Filter_WhereGroup(Ajde_Query::OP_OR);
//			$comparison = is_null($userGroupPart[1]) ? Ajde_Filter::FILTER_IS : Ajde_Filter::FILTER_EQUALS;
//			$group->addFilter(new Ajde_Filter_Where('type', Ajde_Filter::FILTER_EQUALS, $userGroupPart[0], Ajde_Query::OP_AND));
//			if ($userGroupPart[0] !== 'public') {
//				$group->addFilter(new Ajde_Filter_Where($userGroupPart[0], $comparison, $userGroupPart[1], Ajde_Query::OP_AND));
//			}
//			$group->addFilter($moduleActionWhereGroup, Ajde_Query::OP_AND);			
//			$rules->addFilter($group, Ajde_Query::OP_OR);
//		}
		
		$rules->load();
		
		/**
		 * Oempfff... now let's traverse and set the order
		 * 
		 * TODO: It seems that we can just load the entire ACL table in the collection
		 * and use this traversal to find matching rules instead of executing this
		 * overly complicated SQL query constructed above...
		 */
		
		$orderedRules = array();
		foreach($userGroup as $ugpKey => $userGroupPart) {
			$type	= $userGroupPart[0];
			$ugId	= $userGroupPart[1];
			foreach($moduleAction as $maKey => $moduleActionPart) {
				$module = $moduleActionPart['module'];
				$action = $moduleActionPart['action'];
				$extra = $moduleActionPart['extra'];
				$rule = $rules->findRule($type, $ugId, $module, $action, $extra);
				if ($rule !== false) {
					$orderedRules[$ugpKey . $maKey] = $rule;
				}
			}
		}
		
		/**
		 * Finally, determine access
		 */
		$extra = ($extra !== '*') ? ' (' . $extra . ')' : '';
		foreach($orderedRules as $key => $rule) {
			if ($rule->type === 'public') {
				Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' allows access for '.$module.'/'.$action.$extra.' (public)';
				$access = true;
			} else {
				if (parent::getLoggedInUser()) {
					switch ($rule->permission) {
						case "deny":
							Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' denies access for '.$module.'/'.$action.$extra;
							$access = false;
							break;
						case "own":
							if ((int) $this->getOwnerId() === (int) $uid) {
								Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' allows access for '.$module.'/'.$action.$extra.' (owner)';
								$access = true;
							} else {
								Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' denies access for '.$module.'/'.$action.$extra.' (owner)';
								// TODO: or inherit?
								$access = false;
							}
							break;
						case "allow":
							Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' allows access for '.$module.'/'.$action.$extra;
							$access = true;
							break;
					}
				} else {
					Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' denies access for '.$module.'/'.$action.$extra.' (not logged in)';
					$access = false;
				}
			}
		}
		Ajde_Acl::$access = $access;
		return $access;
		
	}
	
	protected function hasAccess()
	{
		if (!isset($this->_hasAccess)) {
			$aclTimer = Ajde::app()->addTimer("<i>ACL validation</i>");
			$this->_hasAccess = $this->validateAccess();
			Ajde::app()->endTimer($aclTimer);
		}
		return $this->_hasAccess;
	}
}