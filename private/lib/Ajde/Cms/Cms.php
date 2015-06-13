<?php

class Ajde_Cms extends Ajde_Object_Singleton
{
    private $_homepageSet = false;

    /**
     * @var NodeModel|boolean
     */
    private $_detectedNode = false;

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
        Ajde_Event::register('Ajde_Core_Route', 'onAfterLangSet', array($this, 'setHomepage'));
        Ajde_Event::register('Ajde_Core_Route', 'onAfterRouteSet', array($this, 'detectNodeSlug'));
        Ajde_Event::register('Ajde_Core_Route', 'onAfterRouteSet', array($this, 'detectShopSlug'));
        return true;
    }

    public function setHomepage(Ajde_Core_Route $route)
    {
        if ($this->_homepageSet) return;
        $this->_homepageSet = true;

        $homepageNodeId = (int) SettingModel::byName('homepage');

        if ($homepageNodeId) {
            $node = NodeModel::fromPk($homepageNodeId);
            if ($node) {
                Config::getInstance()->homepageRoute = $node->getUrl();
            }
        }
    }

    public function detectNodeSlug(Ajde_Core_Route $route)
    {
        $slug = $route->getRoute();

        $slug = trim($slug, '/');
        $lastSlash = strrpos($slug, '/');
        if ($lastSlash !== false) {
            $slug = substr($slug, $lastSlash + 1);
        }

        $node = NodeModel::fromSlug($slug);
        if ($node) {
            $this->_detectedNode = $node;
            $route->setRoute($slug);
            $routes = Config::get('routes');
            array_unshift($routes, array('%^(' . preg_quote($slug) . ')$%' => array('slug')));
            Config::getInstance()->routes = $routes;
        }
    }

    public function detectShopSlug(Ajde_Core_Route $route)
    {
        $slug = $route->getRoute();

        $slug = trim($slug, '/');
        $lastSlash = strrpos($slug, '/');
        if ($lastSlash !== false) {
            $lastSlugPart = substr($slug, $lastSlash + 1);

            $product = ProductModel::fromSlug($lastSlugPart);
            if ($product) {
                $route->setRoute($slug);
                $routes = Config::get('routes');
                array_unshift($routes, array('%^(shop)/(' . preg_quote($lastSlugPart) . ')$%' => array('module', 'slug')));
                Config::getInstance()->routes = $routes;
            }
        }
    }

    /**
     * @return NodeModel|boolean
     */
    public function getRoutedNode()
    {
        return $this->_detectedNode;
    }
}