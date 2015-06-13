<?php

class Ajde_Mailer_CssInliner_Emogrifier implements Ajde_Mailer_CssInliner_CssInlinerInterface
{
    /**
     * @param string $html
     * @return mixed
     */
    public static function inlineCss($html)
    {
        $emogrifier = new \Pelago\Emogrifier($html);
        return $emogrifier->emogrify();
    }
}