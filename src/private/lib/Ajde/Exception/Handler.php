<?php

class Ajde_Exception_Handler extends Ajde_Object_Static
{
	public static function __bootstrap()
	{
		// making xdebug.overload_var_dump = 1 work
		//if (Config::get('debug')) {
			ini_set('html_errors', 1);
		//}
		// TODO: why is this defined here? also in index.php!
		set_error_handler(array('Ajde_Exception_Handler', 'errorHandler'));
		set_exception_handler(array('Ajde_Exception_Handler', 'handler'));
		return true;
	}

	public static function errorHandler($errno, $errstr, $errfile, $errline)
	{
		error_log(sprintf("PHP error: %s in %s on line %s", $errstr, $errfile, $errline));
		// TODO: only possible in PHP >= 5.3 ?
		try
		{
			throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
		} catch(Exception $exception) {
		}
	}

	public static function handler(Exception $exception)
	{
		try
		{	
			if (Config::getInstance()->debug === true)
			{
				if (!((get_class($exception) == 'Ajde_Exception' || is_subclass_of($exception, 'Ajde_Exception')) && !$exception->traceOnOutput())) {
					Ajde_Exception_Log::logException($exception);				
					echo self::trace($exception);
				} else {
					Ajde_Exception_Log::logException($exception);
					Ajde_Http_Response::redirectServerError();
				}
			}
			else
			{
				Ajde_Exception_Log::logException($exception);				
				Ajde_Http_Response::redirectServerError();
			}
		}
		catch (Exception $exception)
		{
			error_log(self::trace($exception, self::EXCEPTION_TRACE_LOG));
			die("An uncatched exception occured within the error handler, see the server error_log for details");
		}
	}

	const EXCEPTION_TRACE_HTML = 1;
	const EXCEPTION_TRACE_LOG = 2;

	public static function trace(Exception $exception, $output = self::EXCEPTION_TRACE_HTML)
	{
		if (Ajde::app()->hasDocument() && Ajde::app()->getDocument()->getFormat() == 'json') {
			$output = self::EXCEPTION_TRACE_LOG;
		}
		if ($exception instanceof ErrorException) {
			$type = "PHP Error " . self::getErrorType($exception->getSeverity());
		} elseif ($exception instanceof Ajde_Exception) {
			$type = "Uncaught application exception" . ($exception->getCode() ? ' ' . $exception->getCode() : '');
		} else {
			$type = "Uncaught PHP exception " . $exception->getCode();
		}

		switch ($output) {
			case self::EXCEPTION_TRACE_HTML:
				if (ob_get_level()) {
					ob_clean();
				}
				
				$traceMessage = '<ol reversed="reversed">';
				self::$firstApplicationFileExpanded = false; 
				foreach($exception->getTrace() as $item) {
					$arguments = null;
					if (!empty($item['args'])) {
						ob_start();
						var_dump($item['args']);
						$dump = ob_get_clean();
						$arguments = sprintf(' with arguments: %s', $dump);
					}
					$traceMessage .= sprintf("<li><em>%s</em>%s<strong>%s</strong><br/>in %s<br/>&nbsp;\n",							
							!empty($item['class']) ? $item['class'] : '&lt;unknown class&gt;',
							!empty($item['type']) ? $item['type'] : '::',
							!empty($item['function']) ? $item['function'] : '&lt;unknown function&gt;',
							self::embedScript(
									issetor($item['file'], null),
									issetor($item['line'],null),
									$arguments,
									false									
							));					
					$traceMessage .= '</li>';
				}
				$traceMessage .= '</ol>';
				
				$exceptionDocumentation = '';
				if ($exception instanceof Ajde_Exception && $exception->getCode()) {
					$exceptionDocumentation = sprintf("<div style='margin-top: 4px;'><img src='//" . Config::get('site_root') . "public/images/_core/globe_16.png' style='vertical-align: bottom;' title='Primary key' width='16' height='16' /> <a href='%s'>Documentation on error %s</a>&nbsp;</div>",
						Ajde_Core_Documentation::getUrl($exception->getCode()),
						$exception->getCode()
					);
				}
				
				$exceptionMessage = sprintf("<div style='background-color:#F1F1F1;background-image: url(\"//" . Config::get('site_root') . "public/images/_core/warning_48.png\"); background-repeat: no-repeat; background-position: 10px 10px; border: 1px solid silver; padding: 10px 10px 10px 70px;'><h3 style='margin:0;'>%s:</h3><h2 style='margin:0;'>%s</h2> Exception thrown in %s%s</div><h3>Trace:</h3>\n",
						$type,
						$exception->getMessage(),
						self::embedScript(
								$exception->getFile(),
								$exception->getLine(),
								$arguments,
								false //!self::$firstApplicationFileExpanded
						),
						$exceptionDocumentation
				);
				
				$exceptionDump = '';
				if (class_exists("Ajde_Dump")) {
					if ($dumps = Ajde_Dump::getAll()) {
						$exceptionDump .= '<h2>Dumps</h2>';
						foreach($dumps as $dump) {
							ob_start();
							var_dump($dump[0]);
							$exceptionDump  .= ob_get_clean();
						}			
					} 
				}
				
				$style = false;
				if (file_exists(MODULE_DIR . '_core/res/css/debugger/handler.css')) {
					$style = file_get_contents(MODULE_DIR . '_core/res/css/debugger/handler.css');
				}
				if ($style === false) {
					// For shutdown() call
					$style = 'body {font: 13px sans-serif;} a {color: #005D9A;} a:hover {color: #9A0092;} h2 {color: #005D9A;} span > a {color: #9A0092;}';
				}
				$style = '<style>' . $style . '</style>';

				$message = $style . $exceptionDump . $exceptionMessage . $traceMessage;
				break;
			case self::EXCEPTION_TRACE_LOG:
				$message = 'Request ' . $_SERVER["REQUEST_URI"] . " triggered:" . PHP_EOL;
				$message .= sprintf("%s: %s in %s on line %s",
						$type,
						$exception->getMessage(),
						$exception->getFile(),
						$exception->getLine()
				);
				foreach(array_reverse($exception->getTrace()) as $i => $line) {
					$message .= PHP_EOL;
					$message .= $i . '. ' . $line['file'] . ' on line ' . $line['line'];
				}				
				break;
		}
		return $message;
	}
	
	public static function getErrorType($type)
	{
		switch ($type)
		{
			case 1: return "E_ERROR";
			case 2: return "E_WARNING";
			case 4: return "E_PARSE";
			case 8: return "E_NOTICE";
			case 16: return "E_CORE_ERROR";
			case 32: return "E_CORE_WARNING";
			case 64: return "E_COMPILE_ERROR";
			case 128: return "E_COMPILE_WARNING";
			case 256: return "E_USER_ERROR";
			case 512: return "E_USER_WARNING";
			case 1024: return "E_USER_NOTICE";
			case 2048: return "E_STRICT";
			case 4096: return "E_RECOVERABLE_ERROR";
			case 8192: return "E_DEPRECATED";
			case 16384: return "E_USER_DEPRECATED";
			case 30719: return "E_ALL";
		}
	}

	static $firstApplicationFileExpanded = false;
	 
	protected static function embedScript($filename = null, $line = null, $arguments = null, $expand = false)
	{
		$lineOffset = 5;
		$file = '';
		if (isset($filename) && isset($line))
		{
			$lines = file($filename);
			for ($i = max(0, $line - $lineOffset - 1); $i < min($line + $lineOffset, count($lines)); $i++)
			{
				$lineNumber = str_repeat(" ", 4 - strlen($i + 1)) . ($i + 1);
				if ($i == $line - 1)
				{
					$file .= "{{{}}}" . $lineNumber . ' ' . ($lines[$i]) . "{{{/}}}";
				}
				else
				{
					$file .= $lineNumber . ' ' . ($lines[$i]);
				}
			}
		}
		
		if (substr_count($filename, str_replace('/', DIRECTORY_SEPARATOR, APP_DIR))) {
			$filename = '<span style="color: red;">' . $filename . '</span>';
			if (self::$firstApplicationFileExpanded === false) {
				$expand = true;
			}
			self::$firstApplicationFileExpanded = true; 
		}
		$file = highlight_string("<?php".$file, true);
		$file = str_replace('&lt;?php', '', $file);
		$file = str_replace('<code>', "<code style='display:block;border:1px solid silver;margin:5px 0 5px 0;background-color:#f1f1f1;'>", $file);
		$file = str_replace('{{{}}}', "<div style='background-color: #ffff9e;'>", $file);
		$file = str_replace('{{{/}}}', "</div>", $file);

		$id = md5(microtime());
		return sprintf(
				"<a
					onclick='document.getElementById(\"$id\").style.display = document.getElementById(\"$id\").style.display == \"block\" ? \"none\" : \"block\";'
					href='javascript:void(0);'
				><i>%s</i> on line <b>%s</b></a>&nbsp;<div id='$id' style='display:%s;'>%s%s</div>",
				$filename,
				$line,
				$expand ? "block" : "none",
				$file,
				$arguments
		);
	}
	
}