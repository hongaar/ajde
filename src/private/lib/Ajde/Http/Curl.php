<?php

class Ajde_Http_Curl {
	
	/**
	 *
	 * @param string $value
	 * @param string $key
	 * @return string
	 */
	static private function rawURLEncodeCallback($value, $key) {
		return "$key=" . rawurlencode($value);
	}
	
	/**
	 *
	 * @param string $url
	 * @param array $postData
	 * @deprecated
	 * @throws Ajde_Core_Exception_Deprecated 
	 */
	static public function doPostRequest($url, $postData) {
		// TODO:
		throw new Ajde_Core_Exception_Deprecated();
	}
	
	/**
	 *
	 * @param string $url
	 * @param array $postData
	 * @return string
	 * @throws Exception 
	 */
	static public function post($url, $postData) {
		$encodedVariables = array_map ( array("Ajde_Http_Curl", "rawURLEncodeCallback"), $postData, array_keys($postData) );
		$postContent = join('&', $encodedVariables);
		$postContentLen = strlen($postContent);
		
		$output = false;
		try {
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);			
			curl_setopt($ch, CURLOPT_URL, $url);			// The URL to fetch. This can also be set when initializing a session with curl_init().
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
			curl_setopt($ch, CURLOPT_HEADER, false);		// TRUE to include the header in the output.

			// Not possible in SAFE_MODE
			//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // TRUE to follow any "Location: " header that the server sends as part of the HTTP header (note this is recursive, PHP will follow as many "Location: " headers that it is sent, unless CURLOPT_MAXREDIRS is set).
			
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10);		// The maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);	// The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);			// The maximum number of seconds to allow cURL functions to execute.
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" ); // The contents of the "User-Agent: " header to be used in a HTTP request.
			curl_setopt($ch, CURLOPT_ENCODING, "");			// The contents of the "Accept-Encoding: " header. This enables decoding of the response. Supported encodings are "identity", "deflate", and "gzip". If an empty string, "", is set, a header containing all supported encoding types is sent.
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);	// TRUE to automatically set the Referer: field in requests where it follows a Location: redirect.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// FALSE to stop cURL from verifying the peer's certificate. Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or a certificate directory can be specified with the CURLOPT_CAPATH option. CURLOPT_SSL_VERIFYHOST may also need to be TRUE or FALSE if CURLOPT_SSL_VERIFYPEER is disabled (it defaults to 2).
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				"Content-Type: application/x-www-form-urlencoded",
				"Content-Length: $postContentLen"
			)); 
			$output = curl_exec($ch);
			curl_close($ch);
		} catch (Exception $e) {
			throw $e;
		}
		return $output;
	}
	
	/**
	 *
	 * @param string $url
	 * @return string
	 * @throws Exception 
	 */
	static public function get($url) {		
		$output = false;
		try {
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $url);			// The URL to fetch. This can also be set when initializing a session with curl_init().
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);	// TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.			
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);	// The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);			// The maximum number of seconds to allow cURL functions to execute.
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1" ); // The contents of the "User-Agent: " header to be used in a HTTP request.
			curl_setopt($ch, CURLOPT_ENCODING, "");			// The contents of the "Accept-Encoding: " header. This enables decoding of the response. Supported encodings are "identity", "deflate", and "gzip". If an empty string, "", is set, a header containing all supported encoding types is sent.
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);	// TRUE to automatically set the Referer: field in requests where it follows a Location: redirect.
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// FALSE to stop cURL from verifying the peer's certificate. Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or a certificate directory can be specified with the CURLOPT_CAPATH option. CURLOPT_SSL_VERIFYHOST may also need to be TRUE or FALSE if CURLOPT_SSL_VERIFYPEER is disabled (it defaults to 2).
			
			// Not possible in SAFE_MODE
			// curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // TRUE to follow any "Location: " header that the server sends as part of the HTTP header (note this is recursive, PHP will follow as many "Location: " headers that it is sent, unless CURLOPT_MAXREDIRS is set).
			// curl_setopt($ch, CURLOPT_HEADER, false);		// TRUE to include the header in the output.
			// curl_setopt($ch, CURLOPT_MAXREDIRS, 10);		// The maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
			$output = self::_curl_exec_follow($ch, 10, false);
			curl_close($ch);
		} catch (Exception $e) {
			throw $e;
		}
		return $output;
	}
	
	/**
	 * @source http://stackoverflow.com/a/5498992/938297
	 */
	private static function _curl_exec_follow(&$ch, $redirects = 20, $curlopt_header = false) {
		if ((!ini_get('open_basedir') && !ini_get('safe_mode')) || $redirects < 1) {
			curl_setopt($ch, CURLOPT_HEADER, $curlopt_header);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $redirects > 0);
			curl_setopt($ch, CURLOPT_MAXREDIRS, $redirects);
			return curl_exec($ch);
		} else {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FORBID_REUSE, false);

			do {
				$data = curl_exec($ch);
				if (curl_errno($ch))
					break;
				$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if ($code != 301 && $code != 302)
					break;
				$header_start = strpos($data, "\r\n")+2;
				$headers = substr($data, $header_start, strpos($data, "\r\n\r\n", $header_start)+2-$header_start);
				if (!preg_match("!\r\n(?:Location|URI): *(.*?) *\r\n!", $headers, $matches))
					break;
				curl_setopt($ch, CURLOPT_URL, $matches[1]);
			} while (--$redirects);
			if (!$redirects)
				trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
			if (!$curlopt_header)
				$data = substr($data, strpos($data, "\r\n\r\n")+4);
			return $data;
		}
	}	
}