<?php

class Ajde_Core_Updater extends Ajde_Object_Singleton
{
	public $current_version;
	public $available_version;
	public $available_package;
	
	public $repo_tags_url = 'https://api.github.com/repos/hongaar/ajde/tags';
	public $changelog_url = 'https://raw.github.com/hongaar/ajde/master/CHANGELOG.md';
	
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
		$tags = json_decode(Ajde_Http_Curl::get($this->repo_tags_url));
		
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
		return Ajde_Http_Curl::get($this->changelog_url);
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
			case "copy":
				return $this->copyFiles();
				break;
			case "post":
				return $this->postHook();
				break;
		}
		return 'Unknown step in update process';
	}
	
	private function getPackageFile()
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
			// delete old directory
			if (is_dir(TMP_DIR . 'update')) {
				Ajde_FS_Directory::delete(TMP_DIR . 'update');
			}
			
			// get root folder
			$root = $zip->statIndex(0);
			$root = $root['name'];
				
			// extract zip
			$zip->extractTo(TMP_DIR);
			$zip->close();
			
			// rename to update
			rename(TMP_DIR . $root, TMP_DIR . 'update');
			return true;
		}
		return false;
	}
	
	public function cleanCurrent()
	{
		$cleanDirs = array(
				CACHE_DIR
		);
		
		foreach($cleanDirs as $cleanDir) {
			Ajde_FS_Directory::truncate($cleanDir);
		}
		return true;
	}
	
	public function copyFiles()
	{
		set_time_limit(5 * 60);
		
		$updateDir = TMP_DIR . 'update' . DIRECTORY_SEPARATOR;
		
		// make sure we are in the current dir
		chdir(Config::get('local_root'));
		
		// delete index.php
		$delete_tries = 0;
		do {
			$delete_tries++;
			if ($delete_tries > 20) {
				throw new Ajde_Exception('Unable to remove index.php, please try again later');
			}
			if ($delete_tries > 0) {
				sleep(1);
			}
			$deleted = @unlink('index.php');
		} while ($deleted === false);
		
		// directories to overwrite
		$installDirs = array(
				CORE_DIR,
				DEV_DIR,
				LIB_DIR . 'Ajde' . DIRECTORY_SEPARATOR,
				PUBLIC_DIR . 'css' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR,
				PUBLIC_DIR . 'js' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR,
				PUBLIC_DIR . 'media' . DIRECTORY_SEPARATOR . '_core' . DIRECTORY_SEPARATOR,
				PUBLIC_DIR . 'media' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR
		);
		
		// files to overwrite
		$rootFiles = Ajde_FS_Find::findFilenames($updateDir, '*');
		
		if (!is_dir(TMP_DIR . 'update')) {
			throw new Ajde_Exception('Update directory not found');
		}
		
		foreach($installDirs as $installDir) {
			if (is_dir($updateDir . $installDir)) {
				
				// truncate first
				Ajde_FS_Directory::truncate($installDir);
				
				// then copy
				Ajde_FS_Directory::copy($updateDir . $installDir, $installDir);
				
			} else {
				throw new Ajde_Exception('Directory ' . $installDir . ' in update package not found');
			}
		}
		
		foreach($rootFiles as $rootFile) {
			if (is_file($updateDir . $rootFile)) {
				
				// copy files
				copy($updateDir . $rootFile, $rootFile);
				
			}
		}
		
		// cleaning up TMP DIR
		unlink(TMP_DIR . 'update.zip');
		Ajde_FS_Directory::delete(TMP_DIR . 'update');		
		
		return true;
	}
	
	public function postHook()
	{
		return $this->updateDatabase();
	}
	
	public function updateDatabase()
	{
		$db = Ajde_Db::getInstance();
		return $db->update();
	}

}