<?php

class Ajde_Application extends Ajde_Object_Singleton
{
    protected $_timers     = [];
    protected $_timerLevel = 0;

    /**
     *
     * @staticvar Ajde_Application $instance
     * @return Ajde_Application
     */
    public static function getInstance()
    {
        static $instance;

        return $instance === null ? $instance = new self : $instance;
    }

    /**
     *
     * @return Ajde_Application
     */
    public static function app()
    {
        return self::getInstance();
    }

    /**
     *
     * @return Ajde_Application
     */
    public static function create()
    {
        return self::getInstance();
    }

    public function addTimer($description)
    {
        $this->_timers[] = [
            'description' => $description,
            'level'       => $this->_timerLevel,
            'start'       => microtime(true),
            'end'         => null,
            'total'       => null
        ];
        $this->_timerLevel++;

        return $this->getLastTimerKey();
    }

    public function getLastTimerKey()
    {
        end($this->_timers);

        return key($this->_timers);
    }

    public function endTimer($key)
    {
        $this->_timerLevel--;
        $this->_timers[$key]['end']   = $end = microtime(true);
        $this->_timers[$key]['total'] = round(($end - $this->_timers[$key]['start']) * 1000, 0);

        return $this->_timers[$key]['total'];
    }

    public function getTimers()
    {
        return $this->_timers;
    }

    public function run()
    {
        // For debugger
        $this->addTimer('<i>Application</i>');

        // Create fresh response
        $timer    = $this->addTimer('Create response');
        $response = new Ajde_Http_Response();
        $this->setResponse($response);
        $this->endTimer($timer);

        Ajde_Event::trigger($this, 'onAfterResponseCreated');

        // Bootstrap init
        $timer     = $this->addTimer('Run bootstrap queue');
        $bootstrap = new Ajde_Core_Bootstrap();
        $bootstrap->run();
        $this->endTimer($timer);

        Ajde_Event::trigger($this, 'onAfterBootstrap');

        // Get request
        $timer   = $this->addTimer('Read in global request');
        $request = $this->loadRequest();
        $this->endTimer($timer);

        Ajde_Event::trigger($this, 'onAfterRequestCreated');

        // Get route
        $timer = $this->addTimer('Initialize route');
        $route = $request->initRoute();
        $this->setRoute($route);
        $this->endTimer($timer);

        Ajde_Event::trigger($this, 'onAfterRouteInitialized');

        // Load document
        $timer    = $this->addTimer('Create document');
        $document = Ajde_Document::fromRoute($route);
        $this->setDocument($document);
        $this->endTimer($timer);

        Ajde_Event::trigger($this, 'onAfterDocumentCreated');

        // Load controller
        $timer      = $this->addTimer('Load controller');
        $controller = Ajde_Controller::fromRoute($route);
        $this->setController($controller);
        $this->endTimer($timer);

        Ajde_Event::trigger($this, 'onAfterControllerCreated');

        // Invoke controller action
        $timer        = $this->addTimer('Invoke controller');
        $actionResult = $controller->invoke();
        $document->setBody($actionResult);
        $this->endTimer($timer);

        Ajde_Event::trigger($this, 'onAfterControllerInvoked');

        // Get document contents
        $timer    = $this->addTimer('Render document');
        $contents = $document->render();
        $this->endTimer($timer);

        Ajde_Event::trigger($this, 'onAfterDocumentRendered');

        // Let the cache handle the contents and have it saved to the response
        $timer = $this->addTimer('Save to response');
        $cache = Ajde_Cache::getInstance();
        $cache->setContents($contents);
        $cache->saveResponse();
        $this->endTimer($timer);

        Ajde_Event::trigger($this, 'onAfterResponseSaved');

        // Output the buffer
        $response->send();

        Ajde_Event::trigger($this, 'onAfterResponseSent');
    }

    public static function routingError(Exception $exception)
    {
        if (Config::get("debug") === true) {
            throw $exception;
        } else {
            if (class_exists('Ajde_Exception_Log')) {
                Ajde_Exception_Log::logException($exception);
            }
            Ajde_Http_Response::redirectNotFound();
        }
    }

    public function loadRequest()
    {
        $request = Ajde_Http_Request::fromGlobal();
        $this->setRequest($request);

        return $request;
    }

    /**
     *
     * @return Ajde_Http_Request
     */
    public function getRequest()
    {
        if (!$this->has("request")) {
            $this->loadRequest();
        }

        return $this->get("request");
    }

    /**
     *
     * @return Ajde_Http_Response
     */
    public function getResponse()
    {
        return $this->get("response");
    }

    /**
     *
     * @return Ajde_Core_Route
     */
    public function getRoute()
    {
        return $this->get("route");
    }

    /**
     *
     * @return Ajde_Document
     */
    public function getDocument()
    {
        return $this->get("document");
    }

    /**
     *
     * @return Ajde_Controller
     */
    public function getController()
    {
        return $this->get("controller");
    }

    public static function includeFile($filename)
    {
        Ajde_Cache::getInstance()->addFile($filename);
        include $filename;
    }

    public static function includeFileOnce($filename)
    {
        Ajde_Cache::getInstance()->addFile($filename);
        include_once $filename;
    }

}
