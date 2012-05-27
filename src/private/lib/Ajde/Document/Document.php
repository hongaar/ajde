<?php

abstract class Ajde_Document extends Ajde_Object_Standard
{
	const CACHE_CONTROL_PUBLIC = 'public';
	const CACHE_CONTROL_PRIVATE = 'private';
	const CACHE_CONTROL_NOCACHE = 'no-cache';
	
	protected $_cacheControl = self::CACHE_CONTROL_PUBLIC;
	protected $_contentType = 'text/html';
	protected $_maxAge = 604800; // 1 week
	
	public function  __construct()
	{
		$this->setFormat(strtolower(str_replace("Ajde_Document_Format_", '', get_class($this))));
	}
	
	/**
	 *
	 * @param Ajde_Core_Route $route
	 * @return Ajde_Document
	 */
	public static function fromRoute(Ajde_Core_Route $route)
	{
		$format = $route->getFormat();
		$documentClass = "Ajde_Document_Format_" . ucfirst($format);
		if (!Ajde_Core_Autoloader::exists($documentClass)) {
			$exception = new Ajde_Exception("Document format $format not found",
					90009);
			Ajde::routingError($exception);
		}
		return new $documentClass();
	}
	
	/**
	 * @return Ajde_Layout
	 */
	public function setLayout(Ajde_Layout $layout)
	{
		$layout->setDocument($this);
		return $this->set("layout", $layout);
	}	

	/**
	 * @return Ajde_Layout
	 */
	public function getLayout()
	{
		if (!$this->hasLayout())
		{
			// Load default layout into document
			$this->setLayout(new Ajde_Layout(Config::get("layout")));
		}		
		return $this->get("layout");
	}

	/**
	 *
	 * @param string $contents
	 */
	public function setBody($contents)
	{
		$this->set('body', $contents);
	}

	/**
	 *
	 * @return string
	 */
	public function getBody()
	{
		if ($this->has('body')) {
			return $this->get('body');
		} else {
			return '';
		}
	}
	
	public function setTitle($title)
	{
		$this->set('title', $title);
	}
	
	public function getTitle()
	{
		return $this->has('title') ? $this->get('title') : __('Untitled page');
	}
	
	public function getFullTitle()
	{
		$projectTitle = Config::get('sitename');
		if ($this->has('title')) {
			return sprintf(Config::get('titleFormat'),
				$projectTitle,
				$this->get('title')
			);
		} else {
			return $projectTitle;
		}
	}
	
	public function setDescription($description)
	{
		$this->set('description', $description);
	}
	
	public function getDescription()
	{
		$projectDescription = Config::get('description');
		if ($this->has('description')) {
			return $this->get('description');
		} else {
			return $projectDescription;
		}
	}	
	
	public function render()
	{
		return $this->getLayout()->getContents();
	}
	
	public function getCacheControl()
	{
		return $this->_cacheControl;
	}
	
	public function setCacheControl($cacheControl)
	{
		$this->_cacheControl = $cacheControl;
	}
	
	public function getContentType()
	{
		return $this->_contentType;
	}
	
	public function setContentType($mimeType)
	{
		$this->_contentType = $mimeType;
	}
	
	public function getMaxAge()
	{
		return (int) $this->_maxAge;
	}
	
	public function setMaxAge($days)
	{
		$this->_maxAge = (60*60*24 * (int) $days);
	}

	/**
	 *
	 * @param Ajde_Resource $resource
	 */
	public function addResource(Ajde_Resource $resource) {}

	public function getResourceTypes() {}
	
	// Render helpers
	
	/**
	 *
	 * @deprecated
	 * @throws Ajde_Core_Exception_Deprecated 
	 */
	protected function setContentTypeHeader($contentType = null)
	{
		throw new Ajde_Core_Exception_Deprecated();
	}
	
	/**
	 *
	 * @deprecated
	 * @throws Ajde_Core_Exception_Deprecated 
	 */
	protected function setCacheControlHeader($cacheControl = null)
	{
		throw new Ajde_Core_Exception_Deprecated();
	}
	
	public static function registerDocumentProcessor($format, $registerOn = 'layout')
	{
		$documentProcessors = Config::get('documentProcessors');
		if (is_array($documentProcessors) && isset($documentProcessors[$format])) {
			foreach($documentProcessors[$format] as $processor) {
				$processorClass = 'Ajde_Document_Processor_' . ucfirst($format) . '_' . $processor;
				if (!Ajde_Core_Autoloader::exists($processorClass)) {
					// TODO:
					throw new Ajde_Exception('Processor ' . $processorClass . ' not found', 90022);
				}
				if ($registerOn == 'layout') {
					Ajde_Event::register('Ajde_Layout', 'beforeGetContents', $processorClass . '::preProcess');
					Ajde_Event::register('Ajde_Layout', 'afterGetContents', $processorClass . '::postProcess');
				} elseif($registerOn == 'compressor') {
					Ajde_Event::register('Ajde_Resource_Local_Compressor', 'beforeCompress', $processorClass . '::preCompress');
					Ajde_Event::register('Ajde_Resource_Local_Compressor', 'afterCompress', $processorClass . '::postCompress');
				} else {
					// TODO:
					throw new Ajde_Exception('Document processor must be registered on either \'layout\' or \'compressor\'');
				}
			}
		}
	}
}