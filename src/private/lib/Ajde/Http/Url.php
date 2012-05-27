<?php

/**
 * PHP URL CLASS
 * 
 * Wrapper which uses curl when url fopen wrappers are not available
 * I wrote it when my provider decided to stop supporting fopen wrappers
 * due to exploits so I had to start using curl in all my websites.
 * 
 * USAGE
 *
 * $mode = Url::getMode();
 * // var_dump($mode);
 * $url = "http://www.google.com/";
 * readurl($url);
 * $len = strlen(url_get_contents($url));
 * // var_dump($len);
 * 
 * TEST CONFIGURATION
 * 
 * try modifying/adding this directive to your php.ini:
 * 		allow_url_fopen = 0;
 * and uncommenting/adding this line:
 * 		extension=php_curl.dll
 * 
 * @license It's free dude
 * @author Joram van den Boezem
 * @copyright May 2010, Joram van den Boezem
 * @version 0.1
 */

/**
 * If true, maps readurl() to Url::read() and url_get_contents() to Url::getContents()
 * @var boolean
 */
define('URL_USE_GLOBAL_FUNCTIONS', true);

class Ajde_Http_Url {

	private static $_mode = null;
	private static $_errMessage = "Function %s not available with this PHP configuration.";

	const MODE_FOPEN	= 1;
	const MODE_CURL		= 2;
	const MODE_NONE		= 3;

	/**
	 * Get supported mode for getting url, prefers fopen
	 * @return integer One of MODE_FOPEN, MODE_CURL, MODE_NONE
	 */
	public static function getMode() {
		if (!isset(self::$_mode)) {
			if (ini_get("allow_url_fopen") == true)  {
				// we have access to fopen url wrappers, use it!
				self::$_mode = self::MODE_FOPEN;
			} elseif (ini_get("allow_url_fopen") == false && function_exists("curl_init")) {
				// we have no access to fopen url wrappers, but we can use curl!
				self::$_mode = self::MODE_CURL;
			} else {
				// we have no access to fopen url wrappers, and no curl :(
				self::$_mode = self::MODE_NONE;
			}
		}
		return self::$_mode;
	}

	/**
	 * Get contents of url with curl
	 * @param string $url
	 * @return string Contents of url
	 */
	private static function _getCurl($url) {
		$output = false;
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);			// The URL to fetch. This can also be set when initializing a session with curl_init().
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_HEADER, false);		// TRUE to include the header in the output.
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // TRUE to follow any "Location: " header that the server sends as part of the HTTP header (note this is recursive, PHP will follow as many "Location: " headers that it is sent, unless CURLOPT_MAXREDIRS is set).
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);		// The maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);	// The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);			// The maximum number of seconds to allow cURL functions to execute.
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" ); // The contents of the "User-Agent: " header to be used in a HTTP request.
			curl_setopt($ch, CURLOPT_ENCODING, "");			// The contents of the "Accept-Encoding: " header. This enables decoding of the response. Supported encodings are "identity", "deflate", and "gzip". If an empty string, "", is set, a header containing all supported encoding types is sent.
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);	// TRUE to automatically set the Referer: field in requests where it follows a Location: redirect.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// FALSE to stop cURL from verifying the peer's certificate. Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or a certificate directory can be specified with the CURLOPT_CAPATH option. CURLOPT_SSL_VERIFYHOST may also need to be TRUE or FALSE if CURLOPT_SSL_VERIFYPEER is disabled (it defaults to 2).
			$output = curl_exec($ch);
			curl_close($ch);
		} catch (Exception $e) {
			throw $e;
		}
		return $output;
	}

	/**
	 * Reads an url and writes it to the output buffer.
	 * @param string $url Name of the url to read.
	 * @return mixed Returns the number of bytes read from the file. If an error occurs, FALSE is returned and unless the function was called as @readurl(), an error message is printed.
	 */
	public static function read($url) {
		switch (self::getMode()) {
			case self::MODE_FOPEN:
				return readfile($url);
			case self::MODE_CURL:
				try {
					$data = self::_getCurl($url);
					echo $data;
					return strlen($data);
				} catch (Exception $e) {
					echo $e->getMessage();
					return false;
				}
			case self::MODE_NONE:
			default:
				throw new Exception(sprintf(self::$_errMessage, "Ajde_Http_Url::read()"));
				return false;
		}
	}

	/**
	 * Reads entire url into a string
	 * @param string $url Name of the url to read.
	 * @return mixed The function returns the read data or FALSE on failure.
	 */
	public static function getContents($url) {
		switch (self::getMode()) {
			case self::MODE_FOPEN:
				return file_get_contents($url);
			case self::MODE_CURL:
				try {
					return self::_getCurl($url);
				} catch (Exception $e) {
					return false;
				}
			case self::MODE_NONE:
			default:
				throw new Exception(sprintf(self::$_errMessage, "Ajde_Http_Url::getContents()"));
				return false;
		}

	}
}

// define global functions

if (URL_USE_GLOBAL_FUNCTIONS) {

	/**
	 * Reads an url and writes it to the output buffer.
	 * @param string $url Name of the url to read.
	 * @return mixed Returns the number of bytes read from the file. If an error occurs, FALSE is returned and unless the function was called as @readurl(), an error message is printed.
	 */
	function readurl($url) { return Ajde_Http_Url::read($url); }

	/**
	 * Reads entire url into a string
	 * @param string $url Name of the url to read.
	 * @return mixed The function returns the read data or FALSE on failure.
	 */
	function url_get_contents($url) { return Ajde_Http_Url::getContents($url); }

}
