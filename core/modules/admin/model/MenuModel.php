<?php

class MenuModel extends Ajde_Model_With_I18n
{
    protected $_autoloadParents = true;
    protected $_displayField    = 'name';

    public function __construct()
    {
        parent::__construct();
        $this->registerEvents();
    }

    public function __wakeup()
    {
        parent::__wakeup();
        $this->registerEvents();
    }

    public function registerEvents()
    {
        if (!Ajde_Event::has($this, 'afterCrudSave', 'postCrudSave')) {
            Ajde_Event::register($this, 'afterCrudSave', 'postCrudSave');
        }
    }

    public function getTreeName()
    {
        $ret = clean($this->name);
        $ret = str_repeat('<span class="tree-spacer"></span>', $this->get('level')) . $ret;

        return $ret;
    }

    public function displayLang()
    {
        Ajde::app()->getDocument()->getLayout()->getParser()->getHelper()->requireCssPublic('core/flags.css');

        $lang        = Ajde_Lang::getInstance();
        $currentLang = $this->get('lang');
        if ($currentLang) {
            $image = '<img src="" class="flag flag-' . strtolower(substr($currentLang, 3,
                    2)) . '" alt="' . $currentLang . '" />';

            return $image . $lang->getNiceName($currentLang);
        }

        return '';
    }

    public function afterSort()
    {
        $this->sortTree('MenuCollection');
    }

    public function postCrudSave(Ajde_Controller $controller, Ajde_Crud $crud)
    {
        $this->sortTree('MenuCollection');
    }

    public function loadByName($name)
    {
        $this->loadByField('name', $name);
    }

    /**
     *
     * @return NodeCollection
     */
    public function getLinks()
    {
        $collection = new NodeCollection();
        $collection->addFilter(new Ajde_Filter_Join('menu', 'menu.node', 'node.id'));
        $collection->addFilter(new Ajde_Filter_Where('menu.parent', Ajde_Filter::FILTER_EQUALS, $this->getPK()));
        $collection->getQuery()->addSelect('menu.name as name');
        $collection->orderBy('menu.sort');

        return $collection;
    }

    public function getItems()
    {
        $collection = new MenuCollection();
        $collection->filterByParent($this->id);
        $collection->orderBy('sort');

        $currentParentUrl = false;
        if ($current = Ajde_Cms::getInstance()->getRoutedNode()) {
            $currentParentUrl = ($current->getParent() && $current->getParent()->hasLoaded()) ? $current->getParent()->getUrl() : false;
        }

        $items = [];
        foreach ($collection as $item) {
            /* @var $item MenuModel */
            $name     = $item->name;
            $target   = "";
            $submenus = [];
            $current  = '';

            $node = new NodeModel();

            if ($item->type == 'URL') {
                $url = $item->url;
                if (substr($url, 0, 7) === 'http://' || substr($url, 0, 8) === 'https://') {
                    $target = "_blank";
                }
            } else {
                if ($item->type == 'Node link') {
                    $node->loadByPK($item->node);
                    $url = $node->getUrl();
                } else {
                    if ($item->type == 'Submenu') {
                        $node->loadByPK($item->node);
                        $url      = $node->getUrl();
                        $submenus = $item->getItems();
                        foreach ($submenus as $submenu) {
                            if ($submenu['current']) {
                                $current = 'active sub-active';
                            }
                        }
                    }
                }
            }

            if ($url == Ajde::app()->getRoute()->getOriginalRoute()) {
                $current = 'active';
            }

            if (Ajde::app()->getRoute()->getOriginalRoute() == '' && $url == config('routes.homepage')) {
                $current = 'active';
            }

            if ($url == $currentParentUrl) {
                $current = 'active sub-active';
            }

            if ($item->type == 'Node link' && !$node->hasLoaded()) {
            } else {
                $items[] = [
                    'node'     => $node,
                    'name'     => $name,
                    'url'      => $url,
                    'target'   => $target,
                    'current'  => $current,
                    'submenus' => $submenus
                ];
            }
        }

        return $items;
    }

}
