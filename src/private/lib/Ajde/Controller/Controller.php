<?php

class Ajde_Controller extends Ajde_Object_Standard
{
	/**
	 * 
	 * @var Ajde_View
	 */
	protected $_view = null;
	
	/**
	 * 
	 * @var Ajde_Core_Route
	 */
	protected $_route = null;
		
	public function  __construct($action = null, $format = null)
	{
		$this->setModule(strtolower(str_replace('Controller', '', get_class($this))));
		if (!isset($action) || !isset($format)) {
			$defaultParts = Config::get('defaultRouteParts');
		}
		$this->setAction(isset($action) ? $action : $defaultParts['action']);
		$this->setFormat(isset($format) ? $format : $defaultParts['format']);
		
		$route = new Ajde_Core_Route($this->getAction());
		$route->setFormat($this->getFormat());
		$this->_route = $route;
	}
	
	public function __fallback($method, $arguments)
	{
		if (Ajde_Event::has('Ajde_Controller', 'call')) {
			return Ajde_Event::trigger('Ajde_Controller', 'call', array($method, $arguments));	
		}
		throw new Ajde_Exception("Call to undefined method ".get_class($this)."::$method()", 90006);		
	}	
		
	public function getModule()
	{
		return $this->get('module');
	}
	
	public function getAction()
	{
		return $this->get('action');
	}
	
	public function getFormat()
	{
		return $this->get('format');
	}
	
	public function getId()
	{
		return $this->get('id');
	}
	
	public function getRoute()
	{
		return $this->_route;
	}

	/**
	 *
	 * @param Ajde_Core_Route $route
	 * @return Ajde_Controller
	 */
	public static function fromRoute(Ajde_Core_Route $route)
	{		
		if ($controller = $route->getController()) {
			$moduleController = ucfirst($route->getModule()) . ucfirst($controller) . 'Controller';
		} else {
			$moduleController = ucfirst($route->getModule()) . 'Controller';
		}
		if (!Ajde_Core_Autoloader::exists($moduleController)) {
			$exception = new Ajde_Exception("Controller $moduleController for module {$route->getModule()} not found",
					90008);
			Ajde::routingError($exception);
		}
		$controller = new $moduleController($route->getAction(), $route->getFormat());
		$controller->_route = $route;
		foreach ($route->values() as $part => $value) {
			$controller->set($part, $value);
		}		
		return $controller;
	}

	public function invoke($action = null, $format = null)
	{
		$timerKey = Ajde::app()->addTimer((string) $this->_route);
		$action = issetor($action, $this->getAction());
		$format = issetor($format, $this->getFormat());
		$emptyFunction = $action;
		$defaultFunction = $action . "Default";
		$formatFunction = $action . ucfirst($format);
		if (method_exists($this, $formatFunction)) {
			$actionFunction = $formatFunction;
		} elseif (method_exists($this, $defaultFunction)) {
			$actionFunction = $defaultFunction;
		} elseif (method_exists($this, $emptyFunction)) {
			$actionFunction = $emptyFunction;
		} else {
			$exception = new Ajde_Exception(sprintf("Action %s for module %s not found",
						$this->getAction(),
						$this->getModule()
					), 90011);
			Ajde::routingError($exception);
		}
		$return = true;
		if (method_exists($this, 'beforeInvoke')) {
			$return = $this->beforeInvoke();
			if ($return !== true && $return !== false) {
				// TODO:
				throw new Ajde_Exception(sprintf("beforeInvoke() must return either TRUE or FALSE"));
			}
		}		
		if ($return === true) {
			$return = $this->$actionFunction();
			if (method_exists($this, 'afterInvoke')) {
				$this->afterInvoke();
			}	
		}			
		Ajde::app()->endTimer($timerKey);
		return $return;

	}

	/**
	 * 
	 * @return Ajde_View
	 */
	public function getView()
	{
		if (!isset($this->_view)) {
			$this->_view = Ajde_View::fromController($this);
		}
		return $this->_view;
	}
	
	/**
	 * 
	 * @param Ajde_View $view
	 */
	public function setView(Ajde_View $view)
	{
		$this->_view = $view;
	}
	
	/**
	 * Shorthand for $controller->getView()->getContents();
	 */
	public function render()
	{
		$return = true;
		if (method_exists($this, 'beforeRender')) {
			$return = $this->beforeRender();
			if ($return !== true && $return !== false) {
				// TODO:
				throw new Ajde_Exception(sprintf("beforeRender() must return either TRUE or FALSE"));
			}
		}
		if ($return === true) {
			return $this->getView()->getContents();
		}		
	}
	
	public function loadTemplate()
	{
		throw new Ajde_Core_Exception_Deprecated();
		$view = Ajde_View::fromController($this);
		return $view->getContents();
	}

	public function redirect($route = Ajde_Http_Response::REDIRECT_SELF)
	{
		Ajde::app()->getResponse()->setRedirect($route);
	}
	
	public function rewrite($route)
	{
		Ajde::app()->getResponse()->dieOnRoute($route);
	}
	
	public function updateCache()
	{
		// TODO:
		throw new Ajde_Core_Exception_Deprecated();
	}
	
	/**
	 *
	 * @param Ajde_Model|Ajde_Collection $object 
	 */
	public function touchCache($object = null)
	{
		Ajde_Cache::getInstance()->updateHash(isset($object) ? $object->hash() : time());
	}
}