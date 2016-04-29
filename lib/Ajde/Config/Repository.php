<?php

class Ajde_Config_Repository extends Ajde_Object_Standard
{
    /**
     * TODO
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->readConfigDir($directory);
        $this->defaults();
    }

    /**
     * TODO
     *
     * @param string $directory
     * @throws Exception
     */
    public function readConfigDir($directory)
    {
        $environment = Ajde_Environment::current();

        $searchDirs = [
            CORE_DIR . $directory,
            CORE_DIR . $directory . $environment . DS,
            APP_DIR . $directory,
            APP_DIR . $directory . $environment . DS
        ];

        foreach ($searchDirs as $searchDir) {
            foreach (Ajde_Fs_Find::findFiles($searchDir, '*.json') as $configFile) {
                if (!$configData = json_decode(file_get_contents($configFile), true)) {
                    throw new Exception('Config file ' . $configFile . ' contains invalid JSON');
                }

                $this->merge(pathinfo($configFile, PATHINFO_FILENAME), $configData);
            }
        }
    }

    public function defaults()
    {
        // URI fragments
        $this->set("app.protocol", (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) ? 'https://' : 'http://');
        $this->set("app.domain", $_SERVER['SERVER_NAME']);
        $this->set("app.path", str_replace(PUBLIC_DIR . 'index.php', '', $_SERVER['PHP_SELF']));
        $this->set("app.rootUrl", $this->get("app.protocol") . $this->get("app.domain") . $this->get("app.path"));
        $this->set("i18n.rootUrl", $this->get("app.rootUrl"));

        // Set default timezone now
        date_default_timezone_set($this->get("app.timezone"));
    }
}
