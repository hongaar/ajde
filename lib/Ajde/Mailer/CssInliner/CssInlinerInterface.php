<?php

interface Ajde_Mailer_CssInliner_CssInlinerInterface
{
    /**
     * @param string $html
     *
     * @return mixed
     */
    public static function inlineCss($html);
}
