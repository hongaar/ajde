<?php
require_once CORE_DIR . CONFIG_DIR . 'Config_Base.php';

class Config_Application extends Config_Base
{

    // Site parameters
    public $ident = 'project';
    public $sitename = 'Project name';
    public $description = 'Project description';
    public $author = 'Author name';
    public $email = 'info@nabble.nl';
    public $version = [
        'number' => '1',
        'name' => 'version description'
    ];

    public $lang = 'en_GB';

    public $secret = 'RANDOMSTRING';

    public $dbDsn = [
        'host' => 'localhost',
        'dbname' => 'ajde'
    ];
    public $dbUser = 'ajde_user';
    public $dbPassword = 'ajde_pass';

    public $ssoTwitterKey = 'Ryrp5QnYJkjBFDYLUuUt8Q';
    public $ssoTwitterSecret = '3gf4kVcjRchAaIL5gOxVMwGvBZv6c8R3gu1dTwaIiYk';

    public $ssoFacebookKey = '536948643066481';
    public $ssoFacebookSecret = 'a9b01ccbf1da937363b0d84b7cdc5da8';

    public $ssoGoogleKey = '514075591820.apps.googleusercontent.com';
    public $ssoGoogleSecret = 'MS4EgiWPHZAaDko9lG_8oX52';
    public $driveGoogleKey = 'AIzaSyCJcdZdQ3tTineYykoy9mnj1NB4TW0hFfk';

    public $apiKeys = [
        'google' => '',
        'soundcloud' => ''
    ];

    function __construct()
    {
        parent::__construct();
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->sessionSavepath = '~/private/var/tmp'; // '~' gets replaced with local_root
        }
    }

    public function getParentClass()
    {
        return strtolower(str_replace('Config_', '', get_parent_class('Config_Application')));
    }

}
