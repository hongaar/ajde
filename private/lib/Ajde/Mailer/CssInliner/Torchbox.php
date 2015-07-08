<?php

class Ajde_Mailer_CssInliner_Torchbox implements Ajde_Mailer_CssInliner_CssInlinerInterface
{
    /**
     * @param string $html
     * @return mixed
     */
    public static function inlineCss($html)
    {
        $url = 'https://inlinestyler.torchbox.com:443/styler/convert/';
        $data = array(
            'returnraw' => '1',
            'source' => $html
        );
        return Ajde_Http_Curl::post($url, $data);
    }
}