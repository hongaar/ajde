<?php

class Ajde_Core_Updater extends Ajde_Object_Singleton
{
	public $current_version;
	public $available_version;
	public $available_package;
	
	/**
	 *
	 * @staticvar Ajde_Application $instance
	 * @return Ajde_Application
	 */
	public static function getInstance()
	{
		static $instance;
		return $instance === null ? $instance = new self : $instance;
	}
	
	protected function __construct()
	{
		$tags = json_decode(Ajde_Http_Curl::get('https://api.github.com/repos/hongaar/ajde/tags'));
		
		$availableversion = $tags[0]->name;
		$zipball = $tags[0]->zipball_url;
		
		$this->current_version = AJDE_VERSION;
		$this->available_version = $availableversion;
		$this->available_package = $zipball;
	}
	
	public function getCurrentVersion()
	{
		return $this->current_version;
	}
	
	public function getAvailableVersion()
	{
		return $this->available_version;
	}
	
	public function getAvailablePackageURL()
	{
		return $this->available_package;
	}

	public function isUpdateable()
	{
		return version_compare($this->available_version, $this->current_version) > 0;
	}
	
	public function getChangelog()
	{
		return Ajde_Http_Curl::get('https://raw.github.com/hongaar/ajde/master/CHANGELOG.md');
	}
	
	public function update($step)
	{
		switch($step) {
			case "download":
				return $this->downloadUpdate();
				break;
			case "extract":
				return $this->extractUpdate();
				break;
			case "clean":
				return $this->cleanCurrent();
				break;
			case "install":
				return $this->installUpdate();
				break;
		}	
	}
	
	public function getPackageFile()
	{
		return TMP_DIR . 'update.zip';
	}
	
	public function downloadUpdate()
	{
		// store in tmp dir
		$url = $this->getAvailablePackageURL();
		$file = $this->getPackageFile();
		if (is_file($file)) {
			unlink($file);
		}
		return Ajde_Http_Curl::download($url, $file);
	}
	
	public function extractUpdate()
	{
		$file = $this->getPackageFile();
		if (!is_file($file)) {
			return false;
		}
		$zip = new ZipArchive;
		$res = $zip->open($file);
		if ($res === true) {
			$zip->extractTo(TMP_DIR);
			$zip->close();
			return true;
		}
		return false;
	}
	
	public function cleanCurrent()
	{
		
	}
	
	public function installUpdate()
	{
		
	}

}