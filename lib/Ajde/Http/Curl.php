<?php

class Ajde_Http_Curl
{

    /**
     *
     * @param string $value
     * @param string $key
     * @return string
     */
    static private function rawURLEncodeCallback($value, $key)
    {
        return "$key=" . rawurlencode($value);
    }

    /**
     *
     * @param string $url
     * @param array $postData
     * @deprecated
     * @throws Ajde_Core_Exception_Deprecated
     */
    static public function doPostRequest($url, $postData)
    {
        // TODO:
        throw new Ajde_Core_Exception_Deprecated();
    }

    /**
     *
     * @param string $url
     * @param array $postData
     * @param string $postType
     * @param array $headers
     * @param string $method
     * @return string
     * @throws Exception
     */
    static public function post($url, $postData, $postType = 'form-urlencoded', $headers = [], $method = 'post')
    {
        if ($postType == 'form-urlencoded') {
            $encodedVariables = array_map(["Ajde_Http_Curl", "rawURLEncodeCallback"], $postData, array_keys($postData));

            $postContent = join('&', $encodedVariables);
            $postContentLen = strlen($postContent);

            $headers = array_merge([
                "Content-Type" => "application/x-www-form-urlencoded",
                "Content-Length" => $postContentLen
            ], $headers);
        } else {
            if ($postType == 'json') {
                $postContent = json_encode($postData);
                $postContentLen = strlen($postContent);

                $headers = array_merge([
                    "Content-Type" => "application/json",
                    "Content-Length" => $postContentLen
                ], $headers);
            }
        }

        $sendHeaders = [];
        foreach ($headers as $k => $v) {
            $sendHeaders[] = $k . ': ' . $v;
        }

        $output = false;

        try {
            $ch = curl_init();

            if ($method == 'post') {
                curl_setopt($ch, CURLOPT_POST, 1);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postContent);
            curl_setopt($ch, CURLOPT_URL,
                $url);            // The URL to fetch. This can also be set when initializing a session with curl_init().
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,
                true);    // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
            curl_setopt($ch, CURLOPT_HEADER, false);        // TRUE to include the header in the output.

            // Not possible in SAFE_MODE
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // TRUE to follow any "Location: " header that the server sends as part of the HTTP header (note this is recursive, PHP will follow as many "Location: " headers that it is sent, unless CURLOPT_MAXREDIRS is set).

            curl_setopt($ch, CURLOPT_MAXREDIRS,
                10);        // The maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,
                5);    // The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
            curl_setopt($ch, CURLOPT_TIMEOUT,
                5);            // The maximum number of seconds to allow cURL functions to execute.
            curl_setopt($ch, CURLOPT_USERAGENT,
                "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36')"); // The contents of the "User-Agent: " header to be used in a HTTP request.
            curl_setopt($ch, CURLOPT_ENCODING,
                "");            // The contents of the "Accept-Encoding: " header. This enables decoding of the response. Supported encodings are "identity", "deflate", and "gzip". If an empty string, "", is set, a header containing all supported encoding types is sent.
            curl_setopt($ch, CURLOPT_AUTOREFERER,
                true);    // TRUE to automatically set the Referer: field in requests where it follows a Location: redirect.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,
                false);// FALSE to stop cURL from verifying the peer's certificate. Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or a certificate directory can be specified with the CURLOPT_CAPATH option. CURLOPT_SSL_VERIFYHOST may also need to be TRUE or FALSE if CURLOPT_SSL_VERIFYPEER is disabled (it defaults to 2).
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,
                false);// 1 to check the existence of a common name in the SSL peer certificate. 2 to check the existence of a common name and also verify that it matches the hostname provided. In production environments the value of this option should be kept at 2 (default value).
            curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
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
     * @param bool|string $toFile
     * @param bool|array $header
     * @return string
     * @throws Exception
     */
    static public function get($url, $toFile = false, $header = false)
    {
        $output = false;
        $debug = false;

        if ($debug) {
            Ajde_Log::_('cURL URL', Ajde_Log::CHANNEL_INFO, Ajde_Log::LEVEL_INFORMATIONAL, $url);
        }

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,
                $url);            // The URL to fetch. This can also be set when initializing a session with curl_init().
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,
                true);    // TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,
                5);    // The number of seconds to wait while trying to connect. Use 0 to wait indefinitely.
            curl_setopt($ch, CURLOPT_TIMEOUT,
                5);            // The maximum number of seconds to allow cURL functions to execute.
            curl_setopt($ch, CURLOPT_USERAGENT,
                "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36"); // The contents of the "User-Agent: " header to be used in a HTTP request.
            curl_setopt($ch, CURLOPT_ENCODING,
                "");            // The contents of the "Accept-Encoding: " header. This enables decoding of the response. Supported encodings are "identity", "deflate", and "gzip". If an empty string, "", is set, a header containing all supported encoding types is sent.
            curl_setopt($ch, CURLOPT_AUTOREFERER,
                true);    // TRUE to automatically set the Referer: field in requests where it follows a Location: redirect.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,
                false);// FALSE to stop cURL from verifying the peer's certificate. Alternate certificates to verify against can be specified with the CURLOPT_CAINFO option or a certificate directory can be specified with the CURLOPT_CAPATH option. CURLOPT_SSL_VERIFYHOST may also need to be TRUE or FALSE if CURLOPT_SSL_VERIFYPEER is disabled (it defaults to 2).
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_COOKIEFILE, "");

            if ($toFile !== false) {

                // @TODO We need SAFE_MODE to be off
                if (ini_get('safe_mode')) {
                    throw new Ajde_Exception('SAFE_MODE must be off when downloading files');
                }

                $fp = fopen($toFile, 'w+'); //This is the file where we save the information

                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS,
                    20);        // The maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                curl_setopt($ch, CURLOPT_FILE, $fp); // write curl response to file
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);

                if ($header) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                }

                curl_exec($ch);

                fclose($fp);
                $output = true;

                $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($debug) {
                    $verbose = curl_getinfo($ch);
                }
                if ($debug) {
                    Ajde_Log::_('cURL result', Ajde_Log::CHANNEL_INFO, Ajde_Log::LEVEL_INFORMATIONAL,
                        var_export($verbose, true));
                }

                curl_close($ch);

                if (substr($http_status, 0, 1 == '4')) {
                    return false;
                }
            } else {
                // Not possible in SAFE_MODE
                // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // TRUE to follow any "Location: " header that the server sends as part of the HTTP header (note this is recursive, PHP will follow as many "Location: " headers that it is sent, unless CURLOPT_MAXREDIRS is set).
                // curl_setopt($ch, CURLOPT_HEADER, false);		// TRUE to include the header in the output.
                // curl_setopt($ch, CURLOPT_MAXREDIRS, 10);		// The maximum amount of HTTP redirections to follow. Use this option alongside CURLOPT_FOLLOWLOCATION.
                $output = self::_curl_exec_follow($ch, 10, false);

                if ($debug) {
                    $verbose = curl_getinfo($ch);
                }
                if ($debug) {
                    Ajde_Log::_('cURL result', Ajde_Log::CHANNEL_INFO, Ajde_Log::LEVEL_INFORMATIONAL,
                        var_export($verbose, true));
                }

                curl_close($ch);
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $output;
    }

    public static function download($url, $filename)
    {
        return self::get($url, $filename);
    }

    /**
     * @source http://stackoverflow.com/a/5498992/938297
     */
    private static function _curl_exec_follow(&$ch, $redirects = 20, $curlopt_header = false)
    {
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
                if (curl_errno($ch)) {
                    break;
                }
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($code != 301 && $code != 302) {
                    break;
                }
                $header_start = strpos($data, "\r\n") + 2;
                $headers = substr($data, $header_start, strpos($data, "\r\n\r\n", $header_start) + 2 - $header_start);

                $headers = explode(PHP_EOL, $headers);
                $redirectFound = false;
                foreach ($headers as $header) {
                    if (preg_match("/(?:Location|URI): (.*)/", $header, $matches)) {
                        $redirectFound = $matches[1];
                    }
                }
                if ($redirectFound === false) {
                    break;
                }

                curl_setopt($ch, CURLOPT_URL, $redirectFound);
            } while (--$redirects);
            if (!$redirects) {
                trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.',
                    E_USER_WARNING);
            }
            if (!$curlopt_header) {
                $data = substr($data, strpos($data, "\r\n\r\n") + 4);
            }

            return $data;
        }
    }
}
