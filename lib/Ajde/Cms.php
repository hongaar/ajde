<?php

class Ajde_Cms extends Ajde_Object_Singleton implements Ajde_BootstrapInterface
{
    private $_homepageSet = false;

    /**
     * @var NodeModel|bool
     */
    private $_detectedNode = false;

    public static function getInstance()
    {
        static $instance;

        return $instance === null ? $instance = new self() : $instance;
    }

    protected function __construct()
    {
    }

    public function __bootstrap()
    {
        Ajde_Event::register('Ajde_Core_Route', 'onAfterLangSet', [$this, 'setHomepage']);
        Ajde_Event::register('Ajde_Core_Route', 'onAfterRouteSet', [$this, 'detectNodeSlug']);
        Ajde_Event::register('Ajde_Core_Route', 'onAfterRouteSet', [$this, 'detectShopSlug']);

        return true;
    }

    public function setHomepage(Ajde_Core_Route $route)
    {
        if ($this->_homepageSet) {
            return;
        }
        $this->_homepageSet = true;

        $homepageNodeId = (int) SettingModel::byName('homepage');

        if ($homepageNodeId) {
            $node = NodeModel::fromPk($homepageNodeId);
            if ($node) {
                Config::set('routes.homepage', $node->getUrl());
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
            $routes = config('routes.list');
            array_unshift($routes, ['%^('.preg_quote($slug).')$%' => ['slug']]);
            Config::set('routes.list', $routes);
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
                $routes = config('routes.list');
                array_unshift($routes, ['%^(shop)/('.preg_quote($lastSlugPart).')$%' => ['module', 'slug']]);
                Config::set('routes.list', $routes);
            }
        }
    }

    /**
     * @return NodeModel|bool
     */
    public function getRoutedNode()
    {
        return $this->_detectedNode;
    }
}
