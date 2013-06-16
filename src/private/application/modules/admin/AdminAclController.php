<?php 

// const in declaration
require MODULE_DIR . 'node/model/NodeModel.php';

class AdminAclController extends AdminController
{	
	private $_pagePermissions = array(
		'Main' => array(
			'all pages' => array(
				'module' => '*',
				'action' => '*',
				'extra' => '*'
			),
			'core' => array(
				'module' => '_core',
				'action' => '*',
				'extra' => '*'
			),
			'dashboard' => array(
				'module' => 'belay',
				'action' => '*',
				'extra' => '*'
			)
		),
		'Nodes' => array(
			'clients' => array(
				'module' => 'client',
				'action' => '*',
				'extra' => '*'
			),
			'projects' => array(
				'module' => 'project',
				'action' => '*',
				'extra' => '*'
			),
			'issues' => array(
				'module' => 'issue',
				'action' => '*',
				'extra' => '*'
			)
		),
		'Reports' => array(
			'todo' => array(
				'module' => 'report',
				'action' => '*',
				'extra' => 'todo'
			),
			'hours' => array(
				'module' => 'report',
				'action' => '',
				'extra' => 'hours'
			),
			'profit' => array(
				'module' => 'report',
				'action' => '*',
				'extra' => 'profit'
			)
		),
		'Admin functions' => array(
			'settings' => array(
				'module' => 'admin',
				'action' => 'settings',
				'extra' => 'cms'
			),
			'users' => array(
				'module' => 'admin',
				'action' => '*',
				'extra' => 'user'
			),
			'access control' => array(
				'module' => 'admin',
				'action' => '*',
				'extra' => 'acl'
			),
			'setup' => array(
				'module' => 'admin',
				'action' => '*',
				'extra' => 'setup'
			)
		),
		'Timer' => array(
			'timer' => array(
				'module' => 'timer',
				'action' => '*',
				'extra' => '*'
			)
		)
	);
	
	private $_modelPermissions = array(
		'Node types' => array(
			'all' => array(
				'model' => 'node',
				'extra' => '*'
			),
			'clients' => array(
				'model' => 'node',
				'extra' => NodeModel::NODETYPE_CLIENT
			),
			'projects' => array(
				'model' => 'node',
				'extra' => NodeModel::NODETYPE_PROJECT
			),
			'streaks' => array(
				'model' => 'node',
				'extra' => NodeModel::NODETYPE_STREAK
			),
			'issue' => array(
				'model' => 'node',
				'extra' => NodeModel::NODETYPE_ISSUE
			),
			'worklog' => array(
				'model' => 'node',
				'extra' => NodeModel::NODETYPE_WORK
			)
		)
	);
	
	public function beforeInvoke($allowed = array()) {
		Ajde_Model::register('acl');
		return parent::beforeInvoke($allowed);
	}
	
	public function view()
	{
		Ajde::app()->getDocument()->setTitle("Access control manager");	
		
		$this->getView()->assign('pagePermissions', $this->_pagePermissions);
		$this->getView()->assign('modelPermissions', $this->_modelPermissions);
	
		return $this->render();
	}
	
	public function page()
	{
		Ajde::app()->getDocument()->setTitle("Page access");	
		
		$page = Ajde::app()->getRequest()->getParam('page');
		$preset = Ajde::app()->getRequest()->getParam('preset');
		$options = $this->_pagePermissions[$page][$preset];
		
		$usergroups = new UsergroupCollection();
		$usergroups->orderBy('sort');
		
		$this->getView()->assign('page', $page);
		$this->getView()->assign('preset', $preset);
		$this->getView()->assign('options', $options);
		$this->getView()->assign('usergroups', $usergroups);
	
		return $this->render();
	}
	
	public function pageJson()
	{
		$usergroup = Ajde::app()->getRequest()->getPostParam('usergroup', array());
		
		$page = Ajde::app()->getRequest()->getPostParam('page');
		$preset = Ajde::app()->getRequest()->getPostParam('preset');
		$options = $this->_pagePermissions[$page][$preset];
		
		foreach($usergroup as $ugId => $permission) {
			AclModel::removePermission($ugId, 'page', $options['module'], $options['action'], $options['extra']);
			if ($permission) {
				AclModel::addPermission($permission, 'page', $ugId, $options['module'], $options['action'], $options['extra']);
			}
		}
		
		Ajde_Session_Flash::alert('Access updated for ' . $page . ': ' . $preset);
		
		return array(
			'success' => true
		);
	}
	
	public function model()
	{
		Ajde::app()->getDocument()->setTitle("Model access");	
		
		$model = Ajde::app()->getRequest()->getParam('model');
		$preset = Ajde::app()->getRequest()->getParam('preset');
		$options = $this->_modelPermissions[$model][$preset];
		
		$usergroups = new UsergroupCollection();
		$usergroups->orderBy('sort');
		
		$this->getView()->assign('model', $model);
		$this->getView()->assign('preset', $preset);
		$this->getView()->assign('options', $options);
		$this->getView()->assign('usergroups', $usergroups);
	
		return $this->render();
	}
	
	public function modelJson()
	{
		$usergroup = Ajde::app()->getRequest()->getPostParam('usergroup', array());
		
		$model = Ajde::app()->getRequest()->getPostParam('model');
		$preset = Ajde::app()->getRequest()->getPostParam('preset');
		$options = $this->_modelPermissions[$model][$preset];
		
		foreach($usergroup as $ugId => $acl) {
			AclModel::removeModelPermissions($ugId, $options['model'], $options['extra']);
			foreach($acl as $permission => $actions) {
				foreach(explode("|", $actions) as $action) {
					if ($action) {
						AclModel::addPermission($permission, 'model', $ugId, $options['model'], $action, $options['extra']);
					}
				}
			}
		}
		
		Ajde_Session_Flash::alert('Access updated for ' . $model . ': ' . $preset);
		
		return array(
			'success' => true
		);
	}
}