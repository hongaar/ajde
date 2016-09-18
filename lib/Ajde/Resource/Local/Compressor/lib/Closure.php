<?php

class Ajde_Resource_Local_Compressor_Js_Closure extends PhpClosure
{
    private $_contents;

    public function __construct($contents)
    {
        $this->_contents = $contents;
        $this->hideDebugInfo();
        $this->simpleMode();
    }

    public function compress()
    {
        return $this->_compile();
    }

    // Overrides

    public function _readSources()
    {
        return $this->_contents;
    }
}

/**
 * Copyright 2010 Daniel Pupius (http://code.google.com/p/php-closure/).
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * PHP wrapper for the Google Closure JS Compiler web service.
 *
 * Handles caching and recompilation of sources.  A recompilation will occur
 * whenever a source file, or the script calling the compiler, changes.
 *
 * The class will handle the Last-Modified and 304 redirects for you, but if
 * you should set Cache-Control headers appropriate for your needs.
 *
 * Example usage:
 *
 * include("php-closure.php");
 *
 * $c = new PhpClosure();
 * $c->add("my-app.js")
 *   ->add("popup.js")
 *   ->advancedMode()
 *   ->useClosureLibrary()
 *   ->cacheDir("/tmp/js-cache/")
 *   ->write();
 *
 * See http://code.google.com/closure/compiler/docs/api-ref.html for more
 * details on the compiler options.
 */
class PhpClosure
{
    public $_srcs = [];
    public $_mode = 'WHITESPACE_ONLY';
    public $_warning_level = 'DEFAULT';
    public $_use_closure_library = false;
    public $_pretty_print = false;
    public $_debug = true;
    public $_cache_dir = '';
    public $_code_url_prefix = '';

    /**
     * Adds a source file to the list of files to compile.  Files will be
     * concatenated in the order they are added.
     */
    public function add($file)
    {
        $this->_srcs[] = $file;

        return $this;
    }

    /**
     * Sets the directory where the compilation results should be cached, if
     * not set then caching will be disabled and the compiler will be invoked
     * for every request (NOTE: this will hit ratelimits pretty fast!).
     */
    public function cacheDir($dir)
    {
        $this->_cache_dir = $dir;

        return $this;
    }

    /**
     * Sets whether to use the Closure Library.  i.e. goog.requires will be
     * resolved and the library code made available.
     */
    public function useClosureLibrary()
    {
        $this->_use_closure_library = true;

        return $this;
    }

    /**
     * Sets the URL prefix to use with the Closure Compiler service's code_url
     * parameter.
     *
     * By default PHP-Closure posts the scripts to the compiler service, however,
     * this is subject to a 200000-byte size limit for the whole post request.
     *
     * Using code_url tells the compiler service the URLs of the scripts to
     * fetch.  The file paths added in add() must therefore be relative to this
     * URL.
     *
     * Example usage:
     *
     * $c->add("js/my-app.js")
     *   ->add("js/popup.js")
     *   ->useCodeUrl('http://www.example.com/app/')
     *   ->cacheDir("/tmp/js-cache/")
     *   ->write();
     *
     * This assumes your PHP script is in a directory /app/ and that the JS is in
     * /app/js/ and accessible via HTTP.
     */
    public function useCodeUrl($code_url_prefix)
    {
        $this->_code_url_prefix = $code_url_prefix;

        return $this;
    }

    /**
     * Tells the compiler to pretty print the output.
     */
    public function prettyPrint()
    {
        $this->_pretty_print = true;

        return $this;
    }

    /**
     * Turns of the debug info.
     * By default statistics, errors and warnings are logged to the console.
     */
    public function hideDebugInfo()
    {
        $this->_debug = false;

        return $this;
    }

    /**
     * Sets the compilation mode to optimize whitespace only.
     */
    public function whitespaceOnly()
    {
        $this->_mode = 'WHITESPACE_ONLY';

        return $this;
    }

    /**
     * Sets the compilation mode to simple optimizations.
     */
    public function simpleMode()
    {
        $this->_mode = 'SIMPLE_OPTIMIZATIONS';

        return $this;
    }

    /**
     * Sets the compilation mode to advanced optimizations (recommended).
     */
    public function advancedMode()
    {
        $this->_mode = 'ADVANCED_OPTIMIZATIONS';

        return $this;
    }

    /**
     * Gets the compilation mode from the URL, set the mode param to
     * 'w', 's' or 'a'.
     */
    public function getModeFromUrl()
    {
        if ($_GET['mode'] == 's') {
            $this->simpleMode();
        } else {
            if ($_GET['mode'] == 'a') {
                $this->advancedMode();
            } else {
                $this->whitespaceOnly();
            }
        }

        return $this;
    }

    /**
     * Sets the warning level to QUIET.
     */
    public function quiet()
    {
        $this->_warning_level = 'QUIET';

        return $this;
    }

    /**
     * Sets the default warning level.
     */
    public function defaultWarnings()
    {
        $this->_warning_level = 'DEFAULT';

        return $this;
    }

    /**
     * Sets the warning level to VERBOSE.
     */
    public function verbose()
    {
        $this->_warning_level = 'VERBOSE';

        return $this;
    }

    /**
     * Writes the compiled response.  Reading from either the cache, or
     * invoking a recompile, if necessary.
     */
    public function write()
    {
        header('Content-Type: text/javascript');

        // No cache directory so just dump the output.
        if ($this->_cache_dir == '') {
            echo $this->_compile();
        } else {
            $cache_file = $this->_getCacheFileName();
            if ($this->_isRecompileNeeded($cache_file)) {
                $result = $this->_compile();
                file_put_contents($cache_file, $result);
                echo $result;
            } else {
                // No recompile needed, but see if we can send a 304 to the browser.
                $cache_mtime = filemtime($cache_file);
                $etag = md5_file($cache_file);
                header('Last-Modified: '.gmdate('D, d M Y H:i:s', $cache_mtime).' GMT');
                header("Etag: $etag");
                if (@strtotime(@$_SERVER['HTTP_IF_MODIFIED_SINCE']) == $cache_mtime ||
                    @trim(@$_SERVER['HTTP_IF_NONE_MATCH']) == $etag
                ) {
                    header('HTTP/1.1 304 Not Modified');
                } else {
                    // Read the cache file and send it to the client.
                    echo file_get_contents($cache_file);
                }
            }
        }
    }

    // ----- Privates -----

    public function _isRecompileNeeded($cache_file)
    {
        // If there is no cache file, we obviously need to recompile.
        if (!file_exists($cache_file)) {
            return true;
        }

        $cache_mtime = filemtime($cache_file);

        // If the source files are newer than the cache file, recompile.
        foreach ($this->_srcs as $src) {
            if (filemtime($src) > $cache_mtime) {
                return true;
            }
        }

        // If this script calling the compiler is newer than the cache file,
        // recompile.  Note, this might not be accurate if the file doing the
        // compilation is loaded via an include().
        if (filemtime($_SERVER['SCRIPT_FILENAME']) > $cache_mtime) {
            return true;
        }

        // Cache is up to date.
        return false;
    }

    public function _compile()
    {
        // Quieten strict notices.
        $code = $originalSize = $originalGzipSize = $compressedSize = $compressedGzipSize = $compileTime = '';

        $tree = $this->_parseXml($this->_makeRequest());

        $result = $tree;
        foreach ($result as $node) {
            switch ($node['tag']) {
                case 'compiledCode':
                    $code = $node['value'];
                    break;
                case 'warnings':
                    $warnings = $node['value'];
                    break;
                case 'errors':
                    $errors = $node['value'];
                    break;
                case 'statistics':
                    foreach ($node['value'] as $stat) {
                        switch ($stat['tag']) {
                            case 'originalSize':
                                $originalSize = $stat['value'];
                                break;
                            case 'originalGzipSize':
                                $originalGzipSize = $stat['value'];
                                break;
                            case 'compressedSize':
                                $compressedSize = $stat['value'];
                                break;
                            case 'compressedGzipSize':
                                $compressedGzipSize = $stat['value'];
                                break;
                            case 'compileTime':
                                $compileTime = $stat['value'];
                                break;
                        }
                    }
                    break;
            }
        }

        $result = '';
        if ($this->_debug) {
            $result = "if(window.console&&window.console.log){\r\n".
                "window.console.log('Closure Compiler Stats:\\n".
                '-----------------------\\n'.
                "Original Size: $originalSize\\n".
                "Original Gzip Size: $originalGzipSize\\n".
                "Compressed Size: $compressedSize\\n".
                "Compressed Gzip Size: $compressedGzipSize\\n".
                "Compile Time: $compileTime\\n".
                'Generated: '.date('Y/m/d H:i:s T')."');\r\n";
            if (isset($errors)) {
                $result .= $this->_printWarnings($errors, 'error');
            }
            if (isset($warnings)) {
                $result .= $this->_printWarnings($warnings, 'warn');
            }
            $result .= "}\r\n\r\n";
        }
        $result .= "$code \r\n";

        return $result;
    }

    public function _printWarnings($warnings, $level = 'log')
    {
        $result = '';
        foreach ($warnings as $warning) {
            $desc = addslashes($warning['value']);
            $type = $warning['attributes']['type'];
            $lineno = $warning['attributes']['lineno'];
            $charno = $warning['attributes']['charno'];
            $line = trim(addslashes($warning['attributes']['line']));
            $result .= "window.console.$level('$type: $desc\\nLine: $lineno\\nChar: $charno\\nLine: $line');\r\n";
        }

        return $result;
    }

    public function _getCacheFileName()
    {
        return $this->_cache_dir.$this->_getHash().'.js';
    }

    public function _getHash()
    {
        return md5(implode(',', $this->_srcs).'-'.
            $this->_mode.'-'.
            $this->_warning_level.'-'.
            $this->_use_closure_library.'-'.
            $this->_pretty_print.'-'.
            $this->_debug);
    }

    public function _getParams()
    {
        $params = [];
        foreach ($this->_getParamList() as $key => $value) {
            $params[] = preg_replace('/_[0-9]$/', '', $key).'='.urlencode($value);
        }

        return implode('&', $params);
    }

    public function _getParamList()
    {
        $params = [];
        if ($this->_code_url_prefix) {
            // Send the URL to each source file instead of the raw source.
            $i = 0;
            foreach ($this->_srcs as $file) {
                $params["code_url_$i"] = $this->_code_url_prefix.$file;
                $i++;
            }
        } else {
            $params['js_code'] = $this->_readSources();
        }
        $params['compilation_level'] = $this->_mode;
        $params['output_format'] = 'xml';
        $params['warning_level'] = $this->_warning_level;
        if ($this->_pretty_print) {
            $params['formatting'] = 'pretty_print';
        }
        if ($this->_use_closure_library) {
            $params['use_closure_library'] = 'true';
        }
        $params['output_info_1'] = 'compiled_code';
        $params['output_info_2'] = 'statistics';
        $params['output_info_3'] = 'warnings';
        $params['output_info_4'] = 'errors';

        return $params;
    }

    public function _readSources()
    {
        $code = '';
        foreach ($this->_srcs as $src) {
            $code .= file_get_contents($src)."\n\n";
        }

        return $code;
    }

    public function _makeRequest()
    {
        $data = $this->_getParams();
        $referer = @$_SERVER['HTTP_REFERER'] or '';

        $fp = fsockopen('closure-compiler.appspot.com', 80) or die('Unable to open socket');

        if ($fp) {
            fwrite($fp, "POST /compile HTTP/1.1\r\n");
            fwrite($fp, "Host: closure-compiler.appspot.com\r\n");
            fwrite($fp, "Referer: $referer\r\n");
            fwrite($fp, "Content-type: application/x-www-form-urlencoded\r\n");
            fwrite($fp, 'Content-length: '.strlen($data)."\r\n");
            fwrite($fp, "Connection: close\r\n\r\n");
            fwrite($fp, $data);

            $result = '';
            while (!feof($fp)) {
                $result .= fgets($fp, 128);
            }

            fclose($fp);
        }

        $data = substr($result, (strpos($result, "\r\n\r\n") + 4));
        if (strpos(strtolower($result), 'transfer-encoding: chunked') !== false) {
            $data = $this->_unchunk($data);
        }

        return $data;
    }

    public function _unchunk($data)
    {
        $fp = 0;
        $outData = '';
        while ($fp < strlen($data)) {
            $rawnum = substr($data, $fp, strpos(substr($data, $fp), "\r\n") + 2);
            $num = hexdec(trim($rawnum));
            $fp += strlen($rawnum);
            $chunk = substr($data, $fp, $num);
            $outData .= $chunk;
            $fp += strlen($chunk);
        }

        return $outData;
    }

    public function _parseXml($data)
    {
        $xml = new SimpleXMLElement($data);

        return $this->_parseXmlHelper($xml);
    }

    public function _parseXmlHelper($xml)
    {
        $tree = null;
        foreach ($xml->children() as $name => $child) {
            $value = (string) $child;
            $node = [
                'tag'   => $name,
                'value' => count($child->children()) == 0 ? $value : $this->_parseXmlHelper($child),
            ];

            foreach ($child->attributes() as $attr => $value) {
                $node['attributes'][$attr] = $value[0];
            }
            $tree[] = $node;
        }

        return $tree;
    }
}
