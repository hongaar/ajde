<?php
/**
 * To be removed in production environment
 */

$absRoot = $_SERVER["SERVER_NAME"] . str_replace('loadtest.php', '', $_SERVER["PHP_SELF"]);
$interval = 50000;

if (isset($_GET['c'])) {
	
	?>
	
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
	
	<?php 
	
	try {
		//@apache_setenv('no-gzip', 1);
	} catch(Exception $e) {}
	@ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);
    for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
    ob_implicit_flush(1);
	
    $sum = array();
	
	echo "<pre>";
    
	for ($i = 0; $i < $_GET['c']; $i++) {
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime;
		
		$temp = file_get_contents('http://'.$absRoot);
		
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = ($endtime - $starttime);
		
		echo "attempt ".($i+1).": <em>" . round($totaltime, 5). "</em> seconds ";
	
		$sum[] = round($totaltime, 5);
		
		// give apache some rest
		for ($j = 0; $j < 10; $j++) {
			echo ".";
			ob_implicit_flush(1);
			usleep($interval / 10);
		}
		
		echo "<br/>";		
		ob_implicit_flush(1);
	}
	
	if ($i > 0) {
		echo '<strong>average  : <em>' . round(array_sum($sum) / $i, 5) . '</em> seconds</strong>';
	} else {
		echo 'Click start testing';
	}
	
	return;
}

?>

<p>
	loadtest. connections: <input type='text' value='5' id="c" />
	<button type='submit' onclick='document.getElementById("test").src="./loadtest.php?c=" + document.getElementById("c").value;'> start testing </button>
</p>

<iframe src='./loadtest.php?c=0' width='960' height='80%' id='test' ></iframe>