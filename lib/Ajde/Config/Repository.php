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
     */
    public function readConfigDir($directory)
    {
        $environment = Environment::current();

        $searchDirs = [
            CORE_DIR . $directory, '*.json',
            CORE_DIR . $directory . $environment . DS, '*.json',
            APP_DIR . $directory, '*.json',
            APP_DIR . $directory . $environment . DS, '*.json'
        ];

        foreach($searchDirs as $searchDir) {
            foreach(Ajde_Fs_Find::findFiles($searchDir, '*.json') as $configFile)
            {
                $this->merge(pathinfo($configFile, PATHINFO_FILENAME), json_decode(file_get_contents($configFile), true));
            }
        }
    }

    public function defaults()
    {
        // URI fragments
        $this->site_protocol = (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS'])) ? 'https://' : 'http://';
        $this->site_domain = $_SERVER['SERVER_NAME'];
        $this->site_path = str_replace('index.php', '', $_SERVER['PHP_SELF']);

        // Assembled URI
        $this->site_root = $this->site_protocol . $this->site_domain . $this->site_path;

        // Assembled URI with language identifier
        $this->lang_root = $this->site_root;

        // Set default timezone now
        date_default_timezone_set($this->timezone);
    }
}
