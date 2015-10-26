<?php

class Ajde_Document_Format_Html extends Ajde_Document
{
    const RESOURCE_POSITION_TOP = 0;

    const RESOURCE_POSITION_FIRST = 1;
    const RESOURCE_POSITION_DEFAULT = 2;
    const RESOURCE_POSITION_LAST = 3;

    // TODO: implement a way to override
    protected $_cacheControl = self::CACHE_CONTROL_NOCACHE;
    protected $_contentType = 'text/html';
    protected $_maxAge = 0; // access

    protected $_resources = [
        self::RESOURCE_POSITION_FIRST => [],
        self::RESOURCE_POSITION_DEFAULT => [],
        self::RESOURCE_POSITION_LAST => []
    ];
    protected $_compressors = [];
    protected $_meta = [];

    public function  __construct()
    {
        /*
         * We add the resources before the template is included, otherwise the
         * layout resources never make it into the <head> section.
         */
        Ajde_Event::register('Ajde_Template', 'beforeGetContents', [$this, 'autoAddResources']);
        Ajde_Event::register('Ajde_Application', 'onAfterDocumentRendered', [$this, 'setupExternalPageCache']);
        parent::__construct();
    }

    public function setupExternalPageCache()
    {
        if (!UserModel::getLoggedIn() && Ajde::app()->getRequest()->method() === 'get' && Config::get('externalPageCache')) {
            $this->setCacheControl(self::CACHE_CONTROL_PUBLIC);
            $this->setMaxAge(1 / 24); // 1 hour
        }
    }

    public function render()
    {
        $this->registerDocumentProcessor('html');

        return parent::render();
    }

    /**
     *
     * @param mixed $resourceTypes
     * @return string
     */
    public function getHead($resourceTypes = '*')
    {
        if (!is_array($resourceTypes)) {
            $resourceTypes = (array)$resourceTypes;
        }

        return $this->renderHead($resourceTypes);
    }

    public function getMeta()
    {
        $code = '';
        foreach ($this->_meta as $meta) {
            $code .= '<meta ' . $meta . ' />';
        }

        return $code;
    }

    public function getScripts()
    {
        return $this->getHead('js');
    }

    public function renderHead(array $resourceTypes = ['*'])
    {
        $code = '';
        $code .= $this->renderResources($resourceTypes);

        return $code;
    }

    public function renderResources(array $types = ['*'])
    {
        return Config::get('compressResources') ?
            $this->renderCompressedResources($types) :
            $this->renderAllResources($types);
    }

    public function renderAllResources(array $types = ['*'])
    {
        $linkCode = '';
        foreach ($this->getResources() as $resource) {
            /* @var $resource Ajde_Resource */
            if (current($types) == '*' || in_array($resource->getType(), $types)) {
                $linkCode .= $resource->getLinkCode() . PHP_EOL;
            }
        }

        return $linkCode;
    }

    public function renderCompressedResources(array $types = ['*'])
    {
        // Reset compressors
        $this->_compressors = [];
        $linkCode = [
            self::RESOURCE_POSITION_FIRST => '',
            self::RESOURCE_POSITION_DEFAULT => '',
            self::RESOURCE_POSITION_LAST => ''
        ];
        foreach ($this->getResources() as $resource) {
            /* @var $resource Ajde_Resource */
            if (current($types) == '*' || in_array($resource->getType(), $types)) {
                if ($resource instanceof Ajde_Resource_Local && !$resource->hasNotEmpty('arguments')) {
                    if (!isset($this->_compressors[$resource->getType()])) {
                        $this->_compressors[$resource->getType()] =
                            Ajde_Resource_Local_Compressor::fromType($resource->getType());
                    }
                    $compressor = $this->_compressors[$resource->getType()];
                    /* @var $compressor Ajde_Resource_Local_Compressor */
                    $compressor->addResource($resource);
                } else {
                    $linkCode[$resource->getPosition()] .= $resource->getLinkCode() . PHP_EOL;
                }
            }
        }
        foreach ($this->_compressors as $compressor) {
            $resource = $compressor->process();
            $linkCode[self::RESOURCE_POSITION_DEFAULT] .= $resource->getLinkCode() . PHP_EOL;
        }

        return $linkCode[self::RESOURCE_POSITION_FIRST] . $linkCode[self::RESOURCE_POSITION_DEFAULT] . $linkCode[self::RESOURCE_POSITION_LAST];
    }

    public function getResourceTypes()
    {
        return [
            Ajde_Resource::TYPE_JAVASCRIPT,
            Ajde_Resource::TYPE_STYLESHEET
        ];
    }

    public function addMeta($contents)
    {
        $this->_meta[] = $contents;
    }

    public function addResource(Ajde_Resource $resource, $position = self::RESOURCE_POSITION_DEFAULT)
    {
        if ($position == self::RESOURCE_POSITION_TOP) {
            $resource->setPosition(self::RESOURCE_POSITION_FIRST);
        } else {
            $resource->setPosition($position);
        }
        // Check for duplicates
        // TODO: another option, replace current resource
        foreach ($this->_resources as $positionArray) {
            foreach ($positionArray as $item) {
                if ((string)$item == (string)$resource) {
                    return false;
                }
            }
        }
        if ($position === self::RESOURCE_POSITION_TOP) {
            array_unshift($this->_resources[self::RESOURCE_POSITION_FIRST], $resource);
        } else {
            $this->_resources[$position][] = $resource;
        }
        // Add to cache
        if ($resource instanceof Ajde_Resource_Local) {
            Ajde_Cache::getInstance()->addFile($resource->getFilename());
        }

        return true;
    }

    public function getResources()
    {
        $return = [];
        foreach ($this->_resources as $positionArray) {
            $return = array_merge($return, $positionArray);
        }

        return $return;
    }

    public function autoAddResources(Ajde_Template $template)
    {
        $position = $template->getDefaultResourcePosition();
        foreach ($this->getResourceTypes() as $resourceType) {
            // default resource
            if ($defaultResource = Ajde_Resource_Local::lazyCreate($resourceType, $template->getBase(), 'default',
                $template->getFormat())
            ) {
                $this->addResource($defaultResource, $position);
            }
            // default sub-action resource
            if (substr_count($template->getAction(), '/') > 0 &&
                $actionDefaultResource = Ajde_Resource_Local::lazyCreate($resourceType, $template->getBase(),
                    $this->_getTemplateActionDefault($template), $template->getFormat())
            ) {
                $this->addResource($actionDefaultResource, $position);
            }
            // non-default resource
            if ($template->getAction() != 'default' &&
                $actionResource = Ajde_Resource_Local::lazyCreate($resourceType, $template->getBase(),
                    $template->getAction(), $template->getFormat())
            ) {
                $this->addResource($actionResource, $position);
            }
        }
    }

    private function _getTemplateActionDefault(Ajde_Template $template)
    {
        $actionArray = explode('/', $template->getAction());
        end($actionArray);
        $actionArray[key($actionArray)] = 'default';

        return implode('/', $actionArray);
    }

}
