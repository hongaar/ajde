<?php

class UserModel extends Ajde_User
{
	public $usernameField = 'username';
	public $passwordField = 'password';
	
	public $defaultUserGroup = self::USERGROUP_USERS;
	
	public function __construct() {
		parent::__construct();
		$this->registerEvents();
		$this->setEncryptedFields(array(
			'email', 'fullname', 'address', 'zipcode', 'city', 'region', 'country'
		));
	}
	
	public function __wakeup()
	{
		parent::__wakeup();
		$this->registerEvents();
	}
	
	public function registerEvents()
	{
		if (!Ajde_Event::has($this, 'afterCrudLoaded', 'parseForCrud')) {
			Ajde_Event::register($this, 'afterCrudLoaded', 'parseForCrud');
			Ajde_Event::register($this, 'beforeCrudSave', 'prepareCrudSave');
		}
	}

    public function beforeInsert()
    {
        $this->saveFileFromWeb('avatar');
    }

    public function beforeSave()
    {
        $this->saveFileFromWeb('avatar');
    }
	
	public function afterSave()
	{
		if ($this->getLoggedIn() && $this->getPK() == $this->getLoggedIn()->getPK()) {
			$this->login();
		}
	}

    private function saveFileFromWeb($fieldName)
    {
        if ($this->has($fieldName) &&
            (substr(strtolower($this->get($fieldName)), 0, 7) === 'http://' ||
                substr(strtolower($this->get($fieldName)), 0, 8) === 'https://')) {

            // load file from web
            $image = Ajde_Http_Curl::get($this->get($fieldName));

            // extract filename (without extension)
            $basename = basename(parse_url($this->get($fieldName), PHP_URL_PATH));
            $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $this->get($this->usernameField)) . '_' . pathinfo($basename, PATHINFO_FILENAME);

            // save to tmp directory
            $tmp_path = TMP_DIR . $filename;
            $fh = fopen($tmp_path, 'wb');
            fwrite($fh, $image);
            fclose($fh);

            // get mime type
            $mimeType = Ajde_FS_File::getMimeType($tmp_path);

            // unlink tmp file
            unlink($tmp_path);

            // get default extension
            $extension = Ajde_FS_File::getExtensionFromMime($mimeType);

            // don't overwrite previous files that were uploaded
            while (is_file(AVATAR_DIR . $filename . '.' . $extension)) {
                $filename .= rand(10, 99);
            }

            // save to avatar directory
            $path = AVATAR_DIR . $filename . '.' . $extension;
            $fh = fopen($path, 'wb');
            fwrite($fh, $image);
            fclose($fh);

            $this->set($fieldName, $filename . '.' . $extension);
        }
    }

    public function copyAvatarFromSso(SsoModel $sso)
    {
        if ($sso->hasNotEmpty('avatar')) {
            $this->set('avatar', $sso->get('avatar'));
            $this->save();
        }
    }
	
	public function emailLink()
	{
		return '<a href="mailto:' . _e($this->getEmail()) . '">' . _e($this->getEmail()) . '</a>';
	}
	
	public function parseForCrud(Ajde_Crud $crud)
	{
		$this->set($this->passwordField, '');
	}
	
	public function getEmail()
	{
		return $this->decrypt('email');
	}
	
	public function getFullname()
	{
		return $this->decrypt('fullname');
	}
	
	public function prepareCrudSave(Ajde_Controller $controller, Ajde_Crud $crud)
	{
		if ($this->hasNotEmpty($this->passwordField)) {
			$password = $this->get($this->passwordField);
			$hash = $this->createHash($password);                
			$this->set($this->passwordField, $hash);
		}
		
		if ($this->hasEmpty('secret')) {
			$this->set('secret', $this->generateSecret());
		}
	}
	
	public function sendResetMail($hash)
	{
		$resetLink = Config::get('site_root') . 'user/reset?h=' . $hash;

		$mailer = new Ajde_Mailer();
        $mailer->sendUsingModel('user_reset_link', $this->getEmail(), $this->getFullname(), array(
            'resetlink' => $resetLink
        ));
	}
	
	public function displayGravatar($width = 90, $class = '')
	{
		return Ajde_Resource_Image_Gravatar::get($this->getEmail(), $width, 'identicon', 'g', true, array('class' => $class));
	}

    public function displayAvatar($width = 90, $class = '')
    {
        if ($this->hasNotEmpty('avatar')) {
            return Ajde_Component_Image::getImageTag(AVATAR_DIR . $this->getAvatar(), $width, $width, true, $class);
        } else {
            return $this->displayGravatar($width, $class);
        }
    }
}