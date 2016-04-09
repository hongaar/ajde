<?php

class Ajde_Core_Autodebug extends Ajde_Object_Singleton
{
    public static function getInstance()
    {
        static $instance;

        return $instance === null ? $instance = new self : $instance;
    }

    static public function __bootstrap()
    {
        if (($user = Ajde_User::getLoggedIn()) && $user->getDebug()) {
            Config::set("app.debug", true);

            $htmlProcessors = config("layout.filters.documentProcessors.html");
            if (is_array($htmlProcessors) && !in_array('Debugger', $htmlProcessors)) {
                $htmlProcessors[] = "Debugger";
                Config::set("layout.filters.documentProcessors.html", $htmlProcessors);
            }
        }

        return true;
    }
}
