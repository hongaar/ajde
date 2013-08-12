<?php

class Ajde_View extends Ajde_Template
{	
	/**
	 * 
	 * @param Ajde_Controller $controller
	 * @return Ajde_View
	 */
	public static function fromController(Ajde_Controller $controller) {
		$base = MODULE_DIR. $controller->getModule() . '/'; 
		$action = $controller->getRoute()->getController() ?
			$controller->getRoute()->getController() . '/' . $controller->getAction() :
			$controller->getAction();			
		$format = $controller->hasFormat() ? $controller->getFormat() : 'html';			
		return new self($base, $action, $format);
	}

	/**
	 *
	 * @param Ajde_Core_Route $route
	 * @return Ajde_View
	 */
	public static function fromRoute($route)
	{
		if (!$route instanceof Ajde_Core_Route) {
			$route = new Ajde_Core_Route($route);
		}
		$base = MODULE_DIR. $route->getModule() . '/';
		$action = $route->getController() ?
			$route->getController() . '/' . $route->getAction() :
			$route->getAction();
		$format = $route->getFormat();
		return new self($base, $action, $format);
	}
}