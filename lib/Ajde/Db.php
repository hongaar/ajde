<?php

class Ajde_Db extends Ajde_Object_Singleton
{
    protected $_adapter = null;
    protected $_tables  = null;

    const FIELD_TYPE_NUMERIC = 'numeric';
    const FIELD_TYPE_TEXT    = 'text';
    const FIELD_TYPE_ENUM    = 'enum';
    const FIELD_TYPE_DATE    = 'date';
    const FIELD_TYPE_SPATIAL = 'spatial';

    /**
     * @return Ajde_Db
     */
    public static function getInstance()
    {
        static $instance;

        return $instance === null ? $instance = new self : $instance;
    }

    protected function __construct()
    {
        $adapterName    = 'Ajde_Db_Adapter_' . ucfirst(Config::get('dbAdapter'));
        $dsn            = Config::get('dbDsn');
        $user           = Config::get('dbUser');
        $password       = Config::get('dbPassword');
        $this->_adapter = new $adapterName($dsn, $user, $password);
    }

    /**
     * @return Ajde_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * @return Ajde_Db_PDO
     */
    public function getConnection()
    {
        return $this->_adapter->getConnection();
    }

    public function getTable($tableName)
    {
        if (!isset($this->_tables[$tableName])) {
            $this->_tables[$tableName] = new Ajde_Db_Table($tableName);
        }

        return $this->_tables[$tableName];
    }

    public function executeFile($filename)
    {
        // @see http://stackoverflow.com/a/10209702/938297

        // time limit
        @set_time_limit(5 * 60);

        // load file
        $commands = file_get_contents($filename);

        // delete comments
        $lines    = explode("\n", $commands);
        $commands = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line && !(substr($line, 0, 2) === '--')) {
                $commands .= $line . "\n";
            }
        }

        // convert to array
        $commands = explode(";" . PHP_EOL, $commands);

        // run commands
        $total = $success = 0;
        foreach ($commands as $command) {
            if (trim($command)) {
                try {
                    $success += ($this->getConnection()->query($command) === false ? 0 : 1);
                } catch (Exception $e) {
                    echo $e->getMessage() . "<br/>";
                }
                $total += 1;
            }
        }

        // return number of successful queries and total number of queries found
        return [
            "success" => $success,
            "total"   => $total
        ];
    }

    public function version()
    {
        $results = $this->getConnection()->query("SELECT v FROM ajde WHERE k = 'version' LIMIT 1");
        foreach ($results as $result) {
            $version = $result[0];
        }

        return $version;
    }

    public function update()
    {
        $dbVersion = $this->version();
        $this->installFromVersion($dbVersion);
        $this->updateVersion();

        return true;
    }

    private function installFromVersion($version = 'v0')
    {
        $sqlFiles = Ajde_Fs_Find::findFiles(DEV_DIR . 'db' . DIRECTORY_SEPARATOR, 'v*.sql');
        usort($sqlFiles, [$this, 'versionSort']);
        foreach ($sqlFiles as $sqlFile) {
            $sqlFileVersion = pathinfo($sqlFile, PATHINFO_FILENAME);
            if (version_compare($sqlFileVersion, $version) > 0) {
                $this->executeFile($sqlFile);
            }
        }
    }

    private function versionSort($a, $b)
    {
        return version_compare($a, $b);
    }

    private function updateVersion($version = AJDE_VERSION)
    {
        $this->getConnection()->query("UPDATE ajde SET v = '" . $version . "' WHERE k = 'version' LIMIT 1");
    }

    public function install()
    {
        if ($this->isInstalled()) {
            die("DB already installed");
        }

        $this->installFromVersion();
        $this->updateVersion();

        die('DB installed. <a href="index.php">Proceed to homepage</a>');
    }

    private function isInstalled()
    {
        return $this->getConnection()->query("SHOW TABLES LIKE 'ajde'")->rowCount() > 0;
    }
}
