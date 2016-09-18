<?php

class Ajde_Mailer_CssInliner_Passthrough implements Ajde_Mailer_CssInliner_CssInlinerInterface
{
    /**
     * @param string $html
     *
     * @return mixed
     */
    public static function inlineCss($html)
    {
        return $html;
    }
}
