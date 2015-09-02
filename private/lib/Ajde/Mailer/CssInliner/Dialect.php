<?php

class Ajde_Mailer_CssInliner_Dialect implements Ajde_Mailer_CssInliner_CssInlinerInterface
{
    /**
     * @param string $html
     * @return mixed
     */
    public static function inlineCss($html)
    {
        $url = 'http://premailer.dialect.ca/api/0.1/documents';
        $data = array(
            'html' => $html,
            'preserve_styles' => 'false'
        );
        $json = Ajde_Http_Curl::post($url, $data);
        $result = @json_decode($json);
        if (is_object($result) && isset($result->documents)) {
            $url = $result->documents->html;
            return Ajde_Http_Curl::get($url);
        }
        return $html;
    }
}