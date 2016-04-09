<?php

// when using const in declaration, uncomment next line
// require CORE_DIR . MODULE_DIR . 'node/model/NodeModel.php';

class AdminAclController extends AdminController
{
    public $_pagePermissions = [
        'General'         => [
            'all pages'     => [
                'module' => '*',
                'action' => '*',
                'extra'  => '*'
            ],
            'core'          => [
                'module' => '_core',
                'action' => '*',
                'extra'  => '*'
            ],
            'administrator' => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => '*'
            ]
        ],
        'Content'         => [
            'nodes' => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'node'
            ],
            'media' => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'media'
            ],
            'menus' => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'menu'
            ],
            'tags'  => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'tag'
            ],
            'email' => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'email'
            ],
            'forms' => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'form'
            ]
        ],
        'Admin functions' => [
            'shop'           => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'shop'
            ],
            'settings'       => [
                'module' => 'admin',
                'action' => 'settings',
                'extra'  => 'cms'
            ],
            'users'          => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'user'
            ],
            'access control' => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'acl'
            ],
            'setup'          => [
                'module' => 'admin',
                'action' => '*',
                'extra'  => 'setup'
            ]
        ]
    ];

    public $_modelPermissions = [
        'General'    => [
            'all models' => [
                'model' => '*',
                'extra' => '*'
            ]
        ],
        'Node types' => [
            'all' => [
                'model' => 'node',
                'extra' => '*'
            ]
        ]
    ];

    public function __construct($action = null, $format = null)
    {
        $nodetypes = new NodetypeCollection();
        $nodetypes->orderBy('sort');
        foreach ($nodetypes as $type) {
            $this->_modelPermissions['Node types'][$type->name] = [
                'model' => 'node',
                'extra' => $type->id
            ];
        }

        Ajde_Event::trigger($this, 'initAclTypes');

        parent::__construct($action, $format);
    }

    public function beforeInvoke($allowed = [])
    {
        return parent::beforeInvoke($allowed);
    }

    public function view()
    {
        Ajde::app()->getDocument()->setTitle("Access control manager");

        $this->getView()->assign('pagePermissions', $this->_pagePermissions);
        $this->getView()->assign('modelPermissions', $this->_modelPermissions);

        return $this->render();
    }

    public function menu()
    {
        $this->getView()->assign('pagePermissions', $this->_pagePermissions);
        $this->getView()->assign('modelPermissions', $this->_modelPermissions);

        return $this->render();
    }

    public function page()
    {
        Ajde::app()->getDocument()->setTitle("Page access");

        $page    = Ajde::app()->getRequest()->getParam('page');
        $preset  = Ajde::app()->getRequest()->getParam('preset');
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
        $usergroup = Ajde::app()->getRequest()->getPostParam('usergroup', []);

        $page    = Ajde::app()->getRequest()->getPostParam('page');
        $preset  = Ajde::app()->getRequest()->getPostParam('preset');
        $options = $this->_pagePermissions[$page][$preset];

        foreach ($usergroup as $ugId => $permission) {
            AclModel::removePermission($ugId, 'page', $options['module'], $options['action'], $options['extra']);
            if ($permission) {
                AclModel::addPermission($permission, 'page', $ugId, $options['module'], $options['action'],
                    $options['extra']);
            }
        }

        Ajde_Session_Flash::alert('Access updated for ' . $page . ': ' . $preset);

        return [
            'success' => true
        ];
    }

    public function model()
    {
        Ajde::app()->getDocument()->setTitle("Model access");

        $model   = Ajde::app()->getRequest()->getParam('model');
        $preset  = Ajde::app()->getRequest()->getParam('preset');
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
        $usergroup = Ajde::app()->getRequest()->getPostParam('usergroup', []);

        $model   = Ajde::app()->getRequest()->getPostParam('model');
        $preset  = Ajde::app()->getRequest()->getPostParam('preset');
        $options = $this->_modelPermissions[$model][$preset];

        foreach ($usergroup as $ugId => $acl) {
            AclModel::removeModelPermissions($ugId, $options['model'], $options['extra']);
            foreach ($acl as $permission => $actions) {
                foreach (explode("|", $actions) as $action) {
                    if ($action) {
                        AclModel::addPermission($permission, 'model', $ugId, $options['model'], $action,
                            $options['extra']);
                    }
                }
            }
        }

        Ajde_Session_Flash::alert('Access updated for ' . $model . ': ' . $preset);

        return [
            'success' => true
        ];
    }
}
