<?php

class Ajde_Mailer_CssInliner_Emogrifier implements Ajde_Mailer_CssInliner_CssInlinerInterface
{
    /**
     * @param string $html
     *
     * @return mixed
     */
    public static function inlineCss($html)
    {
        if (class_exists('\Pelago\Emogrifier')) {
            $emogrifier = new \Pelago\Emogrifier($html);

            return $emogrifier->emogrify();
        } else {
            return $html;
        }
    }
}
