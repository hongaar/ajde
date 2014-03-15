<?php

class Ajde_Cms extends Ajde_Object_Singleton
{
	public static function getInstance()
	{
		static $instance;
		return $instance === null ? $instance = new self : $instance;
	}
	
	protected function __construct()
	{
		Ajde_Model::registerAll();
	}
	
	public function __bootstrap()
	{
		Ajde_Event::register('Ajde_Application', 'onAfterRequestCreated', array($this, 'setHomepage'));
        Ajde_Event::register('Ajde_Core_Route', 'onAfterRouteSet', array($this, 'detectSlug'));
		return true;
	}
	
	public function setHomepage()
	{
		$homepageNodeId = (int) SettingModel::byName('homepage');
		
		if ($homepageNodeId) {
			$node = NodeModel::fromPk($homepageNodeId);
			if ($node) {
				Config::getInstance()->homepageRoute = $node->getUrl();
			}
		}
	}

    public function detectSlug(Ajde_Core_Route $route)
    {
        $slug = $route->getRoute();

        $lastSlash = strrpos($slug, '/');
        if ($lastSlash !== false) {
            $slug = substr($slug, $lastSlash + 1);
        }

        $node = NodeModel::fromSlug($slug);
        if ($node) {
            $route->setRoute($slug);
            $routes = Config::get('routes');
            array_unshift($routes, array('%^(' . preg_quote($slug) . ')$%' => array('slug')));
            Config::getInstance()->routes = $routes;
        }
    }
}