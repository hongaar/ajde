<?php

if (!defined('AJDE')) {
	die('No direct access');
}

global $code;
if (isset($_SERVER['REDIRECT_STATUS'])) {
	$code = $_SERVER['REDIRECT_STATUS'];
	if ($code === '200') { $code = 500; }
} else {
	$code = 500;	
}

function desc() {
	global $code;
	switch ($code) {
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

?>
<!DOCTYPE html> 
<html> 
<head> 
	<title>Server error</title>
</head> 
<body> 
	<h1>ERROR <?php echo $code; ?> - <?php echo desc(); ?></h1>
	<h3>Unfortunately, something went wront.</h3>
	<hr/>
	<p><a href="http://code.google.com/p/ajde">Ajde open framework</a>
	
	<!--
	Adding up to at least 512 bytes with some text from
	http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html
	
	The server has not found anything matching the Request-URI. No
	indication is given of whether the condition is temporary or permanent. The
	410 (Gone) status code SHOULD be used if the server knows, through some
	internally configurable mechanism, that an old resource is permanently
	unavailable and has no forwarding address. This status code is commonly
	used when the server does not wish to reveal exactly why the request has
	been refused, or when no other response is applicable.
	-->
</body> 
</html> 