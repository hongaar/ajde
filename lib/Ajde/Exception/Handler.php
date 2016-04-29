<?php

class Ajde_Exception_Handler extends Ajde_Object_Static
{
    public static function __bootstrap()
    {
        // making xdebug.overload_var_dump = 1 work
        //if (config("app.debug")) {
        ini_set('html_errors', 1);
        //}
        // TODO: why is this defined here? also in index.php!
        set_error_handler(['Ajde_Exception_Handler', 'errorHandler']);
        set_exception_handler(['Ajde_Exception_Handler', 'handler']);

        return true;
    }

    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() > 0) {
            $message = sprintf("PHP error: %s in %s on line %s", $errstr, $errfile, $errline);
            error_log($message);
            dump($message);

            // TODO: only possible in PHP >= 5.3 ?
            //			try
            //			{
            throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
            //			} catch(Exception $exception) {
            //			}
        }
    }

    public static function handler(Exception $exception)
    {
        try {
            if (config("app.debug") === true) {
                if (!((get_class($exception) == 'Ajde_Exception' || is_subclass_of($exception,
                            'Ajde_Exception')) && !$exception->traceOnOutput())
                ) {
                    Ajde_Exception_Log::logException($exception);
                    echo self::trace($exception);
                } else {
                    Ajde_Exception_Log::logException($exception);
                    Ajde_Http_Response::redirectServerError();
                }
            } else {
                Ajde_Exception_Log::logException($exception);
                Ajde_Http_Response::redirectServerError();
            }
        } catch (Exception $exception) {
            error_log(self::trace($exception, self::EXCEPTION_TRACE_LOG));
            die("An uncatched exception occured within the error handler, see the server error_log for details");
        }
    }

    const EXCEPTION_TRACE_HTML = 1;
    const EXCEPTION_TRACE_ONLY = 3;
    const EXCEPTION_TRACE_LOG  = 2;

    public static function trace(Exception $exception, $output = self::EXCEPTION_TRACE_HTML)
    {
        $simpleJsonTrace = false;
        if ($simpleJsonTrace && Ajde::app()->hasDocument() && Ajde::app()->getDocument()->getFormat() == 'json') {
            $output = self::EXCEPTION_TRACE_LOG;
        }

        $type = self::getTypeDescription($exception);

        switch ($output) {
            case self::EXCEPTION_TRACE_HTML:
                if (ob_get_level()) {
                    ob_clean();
                }

                $traceMessage                       = '<ol reversed="reversed">';
                self::$firstApplicationFileExpanded = false;
                foreach ($exception->getTrace() as $item) {
                    $arguments = null;
                    if (!empty($item['args'])) {
                        ob_start();
                        var_dump($item['args']);
                        $dump      = ob_get_clean();
                        $arguments = sprintf(' with arguments: %s', $dump);
                    }
                    $traceMessage .= sprintf("<li><code><em>%s</em>%s<strong>%s</strong></code><br/>in %s<br/>&nbsp;\n",
                        !empty($item['class']) ? $item['class'] : '&lt;unknown class&gt;',
                        !empty($item['type']) ? $item['type'] : '::',
                        !empty($item['function']) ? $item['function'] : '&lt;unknown function&gt;',
                        self::embedScript(
                            issetor($item['file'], null),
                            issetor($item['line'], null),
                            $arguments,
                            false
                        ));
                    $traceMessage .= '</li>';
                }
                $traceMessage .= '</ol>';

                $exceptionDocumentation = '';
                if ($exception instanceof Ajde_Exception && $exception->getCode()) {
                    $exceptionDocumentation = sprintf("<div style='margin-top: 4px;'><img src='" . config("app.rootUrl") . MEDIA_DIR . "_core/globe_16.png' style='vertical-align: bottom;' title='Primary key' width='16' height='16' /> <a href='%s'>Documentation on error %s</a>&nbsp;</div>",
                        Ajde_Core_Documentation::getUrl($exception->getCode()),
                        $exception->getCode()
                    );
                }

                $exceptionMessage = sprintf("<summary style='background-image: url(\"" . config("app.rootUrl") . MEDIA_DIR . "_core/warning_48.png\");'><h3 style='margin:0;'>%s:</h3><h2 style='margin:0;'>%s</h2> Exception thrown in %s%s</summary><h3>Trace:</h3>\n",
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
                if (class_exists(Ajde_Dump::class)) {
                    if ($dumps = Ajde_Dump::getAll()) {
                        $exceptionDump .= '<h2>Dumps</h2>';
                        foreach ($dumps as $source => $dump) {
                            ob_start();
                            echo $source;
                            if (class_exists(Kint::class)) {
                                Kint::dump($dump[0]);
                            } else {
                                echo '<pre>';
                                var_dump($dump[0]);
                                echo '</pre>';
                            }
                            $exceptionDump .= ob_get_clean() . '<h2>Error message</h2>';
                        }
                    }
                }

                $style = false;
                if (file_exists(LOCAL_ROOT . CORE_DIR . MODULE_DIR . '_core/res/css/debugger/handler.css')) {
                    $style = file_get_contents(LOCAL_ROOT . CORE_DIR . MODULE_DIR . '_core/res/css/debugger/handler.css');
                }
                if ($style === false) {
                    // For shutdown() call
                    $style = 'body {font: 13px sans-serif;} a {color: #005D9A;} a:hover {color: #9A0092;} h2 {color: #005D9A;} span > a {color: #9A0092;}';
                }
                $style  = '<style>' . $style . '</style>';
                $script = '<script>document.getElementsByTagName("base")[0].href="";</script>';

                if (Ajde::app()->getRequest()->isAjax()) {
                    $collapsed = $exceptionDump . $exceptionMessage . $traceMessage;
                    $header    = '';
                } else {
                    $collapsed = '<div id="details">' . $exceptionDump . $exceptionMessage . $traceMessage . '</div>';
                    $header    = '<header><h1><img src="' . config("app.rootUrl") . MEDIA_DIR . 'ajde-small.png">Something went wrong</h1><a href="javascript:history.go(-1);">Go back</a> <a href="#details">Show details</a></header>';
                }

                $message = $style . $script . $header . $collapsed;
                break;
            case self::EXCEPTION_TRACE_ONLY:
                $message = '';
                foreach (array_reverse($exception->getTrace()) as $i => $line) {
                    $message .= $i . '. ' . (isset($line['file']) ? $line['file'] : 'unknown file') . ' on line ' . (isset($line['line']) ? $line['line'] : 'unknown line');
                    $message .= PHP_EOL;
                }
                break;
            case self::EXCEPTION_TRACE_LOG:
                $message = 'UNCAUGHT EXCEPTION' . PHP_EOL;
                $message .= "\tRequest " . $_SERVER["REQUEST_URI"] . " triggered:" . PHP_EOL;
                $message .= sprintf("\t%s: %s in %s on line %s",
                    $type,
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                );
                foreach (array_reverse($exception->getTrace()) as $i => $line) {
                    $message .= PHP_EOL;
                    $message .= "\t" . $i . '. ' . (isset($line['file']) ? $line['file'] : 'unknown file') . ' on line ' . (isset($line['line']) ? $line['line'] : 'unknown line');
                }
                break;
        }

        return $message;
    }

    public static function getTypeDescription(Exception $exception)
    {
        if ($exception instanceof ErrorException) {
            $type = "PHP Error " . self::getErrorType($exception->getSeverity());
        } elseif ($exception instanceof Ajde_Exception) {
            $type = "Application exception" . ($exception->getCode() ? ' ' . $exception->getCode() : '');
        } else {
            $type = "PHP exception " . $exception->getCode();
        }

        return $type;
    }

    public static function getExceptionChannelMap(Exception $exception)
    {
        if ($exception instanceof ErrorException) {
            return Ajde_Log::CHANNEL_ERROR;
        } elseif ($exception instanceof Ajde_Core_Exception_Routing) {
            return Ajde_Log::CHANNEL_ROUTING;
        } elseif ($exception instanceof Ajde_Core_Exception_Security) {
            return Ajde_Log::CHANNEL_SECURITY;
        } elseif ($exception instanceof Ajde_Exception) {
            return Ajde_Log::CHANNEL_APPLICATION;
        } else {
            return Ajde_Log::CHANNEL_EXCEPTION;
        }
    }

    public static function getExceptionLevelMap(Exception $exception)
    {
        if ($exception instanceof ErrorException) {
            return Ajde_Log::LEVEL_ERROR;
        } elseif ($exception instanceof Ajde_Core_Exception_Routing) {
            return Ajde_Log::LEVEL_WARNING;
        } elseif ($exception instanceof Ajde_Core_Exception_Security) {
            return Ajde_Log::LEVEL_WARNING;
        } elseif ($exception instanceof Ajde_Exception) {
            return Ajde_Log::LEVEL_ERROR;
        } else {
            return Ajde_Log::LEVEL_ERROR;
        }
    }

    public static function getErrorType($type)
    {
        switch ($type) {
            case 1:
                return "E_ERROR";
            case 2:
                return "E_WARNING";
            case 4:
                return "E_PARSE";
            case 8:
                return "E_NOTICE";
            case 16:
                return "E_CORE_ERROR";
            case 32:
                return "E_CORE_WARNING";
            case 64:
                return "E_COMPILE_ERROR";
            case 128:
                return "E_COMPILE_WARNING";
            case 256:
                return "E_USER_ERROR";
            case 512:
                return "E_USER_WARNING";
            case 1024:
                return "E_USER_NOTICE";
            case 2048:
                return "E_STRICT";
            case 4096:
                return "E_RECOVERABLE_ERROR";
            case 8192:
                return "E_DEPRECATED";
            case 16384:
                return "E_USER_DEPRECATED";
            case 30719:
                return "E_ALL";
        }
    }

    static $firstApplicationFileExpanded = false;

    protected static function embedScript($filename = null, $line = null, $arguments = null, $expand = false)
    {
        $lineOffset = 5;
        $file       = '';

        // in case of eval, filename looks like File.php(30) : eval()'d code
        if (substr_count($filename, '(')) {
            list($filename) = explode('(', $filename);
        }

        if (isset($filename) && isset($line)) {
            $lines = file($filename);
            for ($i = max(0, $line - $lineOffset - 1); $i < min($line + $lineOffset, count($lines)); $i++) {
                $lineNumber = str_repeat(" ", 4 - strlen($i + 1)) . ($i + 1);
                if ($i == $line - 1) {
                    $file .= "{{{}}}" . $lineNumber . ' ' . ($lines[$i]) . "{{{/}}}";
                } else {
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
        $file = highlight_string("<?php" . $file, true);
        $file = str_replace('&lt;?php', '', $file);
        $file = str_replace('<code>',
            "<code style='display:block;border:1px solid silver;margin:5px 0 5px 0;background-color:#f1f1f1;'>", $file);
        $file = str_replace('{{{}}}', "<div style='background-color: #ffff9e;'>", $file);
        $file = str_replace('{{{/}}}', "</div>", $file);

        $id = md5(microtime() . $filename . $line);

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
