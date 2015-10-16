<?php
require_once 'Config_Advanced.php';

class Config_Cms extends Config_Advanced
{
	public $defaultRouteParts	= array(
									'module' => 'node',
									'controller' => null,
									'action' => 'view',
									'format' => 'html',
									'nodetype' => null,
									'slug' => null,
									'id' => null
								);
	public $routes				= array(
									array('%^-([^/\.]+)/([^/\.]+)$%' => array('nodetype', 'slug')),
									array('%^-([^/\.]+)/([^/\.]+)\.(html)$%' => array('nodetype', 'slug', 'format')),
								);

	public $layout				= 'cms';
	public $adminLayout			= 'admin';

	public $dbDsn				= array(
									'host' 		=> 'localhost',
									'dbname'	=> 'ajde_cms'
									);

	public function getParentClass()
	{
		return strtolower(str_replace('Config_', '', get_parent_class('Config_Application')));
	}

}
