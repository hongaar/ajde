<?php

class Ajde_Http_Response extends Ajde_Object_Standard
{
	const REDIRECT_HOMEPAGE = 1;
	const REDIRECT_REFFERER = 2;
	const REDIRECT_SELF		= 3;

	const RESPONSE_TYPE_NOT_MODIFIED = 304;
	const RESPONSE_TYPE_UNAUTHORIZED = 401;
	const RESPONSE_TYPE_FORBIDDEN = 403;
	const RESPONSE_TYPE_NOTFOUND = 404;
	const RESPONSE_TYPE_SERVERERROR = 500;
	
	public static function redirectNotFound()
	{
		self::dieOnCode(self::RESPONSE_TYPE_NOTFOUND);
	}

	public static function redirectServerError()
	{
		self::dieOnCode(self::RESPONSE_TYPE_SERVERERROR);
	}

	public static function dieOnCode($code)
	{		
		self::setResponseType($code);		
		
		header("Content-type: text/html; charset=UTF-8");		
		
		$_SERVER['REDIRECT_STATUS'] = $code;
		
		if (array_key_exists($code, Config::getInstance()->responseCodeRoute)) {
			self::dieOnRoute(Config::getInstance()->responseCodeRoute[$code]);
		} else {
			ob_get_clean();
			include(Config::get('local_root') . '/errordocument.php');
			die();
		}
	}
	
	public static function dieOnRoute($route)
	{
		ob_get_clean();
		// We start a mini app here to display the route
		// Copied from Ajde_Application
		$route = new Ajde_Core_Route($route);		
		$document = Ajde_Document::fromRoute($route);
		// replace document in Ajde_Application
		Ajde::app()->setDocument($document);
		$controller = Ajde_Controller::fromRoute($route);
		$actionResult = $controller->invoke();
		$document->setBody($actionResult);
		if (!$document->hasLayout())
		{
			$layout = new Ajde_Layout(Config::get("layout"));
			$document->setLayout($layout);
		}
		echo $document->render();
		die();
	}

	public static function setResponseType($code)
	{
		header("HTTP/1.0 ".$code." ".self::getResponseType($code));
		ob_get_clean();
		header("Status: ".$code." ".self::getResponseType($code));
	}

	protected static function getResponseType($code)
	{
		switch ($code)
		{
			case 304: return "Not Modified";
			case 400: return "Bad Request";
			case 401: return "Unauthorized";
			case 403: return "Forbidden";
			case 404: return "Not Found";
			case 500: return "Internal Server Error";
			case 501: return "Not Implemented";
			case 502: return "Bad Gateway";
			case 503: return "Service Unavailable";
			case 504: return "Bad Timeout";
		}
	}

	public function setRedirect($url = self::REDIRECT_SELF)
	{
		if ($url === true || $url === self::REDIRECT_HOMEPAGE) {
			$this->addHeader("Location", 'http://' . Config::get('site_root'));
		} elseif ($url === self::REDIRECT_REFFERER) {
			$this->addHeader("Location", Ajde_Http_Request::getRefferer());
		} elseif ($url === self::REDIRECT_SELF || empty($url)) {
			$route = (string) Ajde::app()->getRoute();
			$this->addHeader("Location", 'http://' . Config::get('site_root') . $route);
		} elseif (substr($url, 0, 7) == "http://") {
			$this->addHeader("Location", $url);
		} elseif ($url) {
			$this->addHeader("Location", 'http://' . Config::get('site_root') . $url);
		}
		// Don't load any content after Location header is set
		Ajde::app()->getDocument()->setLayout(new Ajde_Layout('empty'));
	}

	public function addHeader($name, $value)
	{
		$headers = array();
		if ($this->has('headers')) {
			$headers = $this->get('headers');
		}
		$headers[$name] = $value;
		$this->set("headers", $headers);
	}
	
	public function removeHeader($name)
	{
		// TODO: also remove from $this->_data['headers']
		header("$name:");		
		if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
			header_remove($name);
		}
	}

	public function setData($data)
	{
		$this->set("data", $data);
	}

	public function send()
	{
		if ($this->has("headers")) {
			foreach($this->get("headers") as $name => $value) {
				header("$name: $value");
			}
		}

		if (!array_key_exists('Location', $this->get("headers"))) {
			echo $this->getData();
		}
	}
}