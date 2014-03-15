<?php

class AdminSystemController extends AdminController
{
	protected $_allowedActions = array(
			'chromeApp',
			'chromeAppDownload'
		);

	public function check()
	{
		Ajde::app()->getDocument()->setTitle("System check");

		$checks = array();

		$checks[] = array(
			'msg'	=> 'Directories writable?',
			'fn'	=> 'writable'
		);
		$checks[] = array(
			'msg'	=> 'Production ready?',
			'fn'	=> 'production'
		);

		$ret = array();

		foreach($checks as $check) {
			$ret = call_user_func(array($this, 'check' . ucfirst($check['fn'])));
			if (empty($ret)) {
				$ret = array(array('msg' => 'OK', 'status' => 'success'));
			}
			foreach($ret as $re) {
				$results[] = array(
					'check'		=> $check['msg'],
					'msg'		=> $re['msg'],
					'status'	=> $re['status']
				);
			}
		}
		
		$config = Config::getAll();
		$hidden = 'HASH: ';
		$hide = array(
				'dbPassword',
				'secret',
				'shopWedealPassword',
				'shopWedealCallbackPassword'
			);
		foreach($hide as $field) {
			$config[$field] = $hidden . md5($config[$field]);
		}

		$this->getView()->assign('results', $results);
		$this->getView()->assign('config', $config);
		return $this->render();
	}

	public function updateHtml()
	{
		Ajde::app()->getDocument()->setTitle("Ajde updater");

		$updater = Ajde_Core_Updater::getInstance();

		$this->getView()->assign('updater', $updater);
		return $this->render();
	}

	public function updateJson()
	{
		$step = Ajde::app()->getRequest()->getPostParam('step', 'start');
		$status = true;
		if ($step !== 'start') {
			$status = false;
			$updater = Ajde_Core_Updater::getInstance();
			try {
				$status = $updater->update($step);
			} catch(Exception $e) {
				Ajde_Exception_Log::logException($e);
				$status = $e->getMessage();
			}
		}
		return array('status' => $status);
	}

	public function chromeApp()
	{
		Ajde::app()->getDocument()->setTitle("Chrome app");
		return $this->render();
	}

	// @see http://stackoverflow.com/a/5586372/938297
	public function chromeAppDownload()
	{
		// Temp dir
		$appdir = TMP_DIR . 'app' . DIRECTORY_SEPARATOR;
		mkdir($appdir);

		// manifest.json
		$url = Config::get('lang_root') . 'admin/?chromeapp=1';
		$manifest = (object) array(
			"manifest_version" 		=> 2,
			"name" 					=> Config::get('sitename'),
			"description"			=> Config::get('description'),
			"version"				=> "1.0",
			"icons"					=> (object) array("128" => "app.png"),
			"app"					=> (object) array(
					"urls" => array($url),
					"launch" => (object) array("web_url" => $url)
				),
			"permissions" 			=> array("unlimitedStorage", "notifications")
				);
		$json = json_encode($manifest);

		// Clean temp dir
		Ajde_FS_Directory::truncate($appdir);

		// Put files
		file_put_contents($appdir . 'manifest.json', $json);
		copy(MEDIA_DIR . 'app.png', $appdir . 'app.png');

		// All files to zip
		$appfiles = Ajde_FS_Find::findFiles($appdir, '*');

		// Make the zip file
		$zipfile = TMP_DIR . 'app.zip';
		$zip = new ZipArchive();
		$zip->open($zipfile, ZIPARCHIVE::OVERWRITE);
		foreach($appfiles as $file) {
			$zip->addFile($file, basename($file));
		}
		$zip->close();

		// Get contents
		$zipcontents = file_get_contents($zipfile);

		// Path to crx file
		$crxfile = TMP_DIR . 'app.crx';

		// Path to pem file
		$pemfile = DEV_DIR . 'app.pem';
		$pemcontents = file_get_contents($pemfile);

		// Fetch private key from file and ready it
		$privkey = openssl_get_privatekey($pemcontents);

		// Get public key
		$pubkey = openssl_pkey_get_details($privkey);
		$pubkey = $pubkey["key"];

		// geting rid of -----BEGIN/END PUBLIC KEY-----
		$pubkey = explode('-----', $pubkey);
		$pubkey = trim($pubkey[2]);

		// decode the public key
		$pubkey = base64_decode($pubkey);

		// make a SHA1 signature using our private key
		openssl_sign($zipcontents, $signature, $privkey, OPENSSL_ALGO_SHA1);
		openssl_free_key($privkey);

		# .crx package format:
		#
		#   magic number               char(4)
		#   crx format ver             byte(4)
		#   pub key lenth              byte(4)
		#   signature length           byte(4)
		#   public key                 string
		#   signature                  string
		#   package contents, zipped   string
		#
		# see http://code.google.com/chrome/extensions/crx.html
		#
		$fh = fopen($crxfile, 'wb');
		fwrite($fh, 'Cr24');                             // extension file magic number
		fwrite($fh, pack('V', 2));                       // crx format version
		fwrite($fh, pack('V', strlen($pubkey)));         // public key length
		fwrite($fh, pack('V', strlen($signature)));      // signature length
		fwrite($fh, $pubkey);                            // public key
		fwrite($fh, $signature);                         // signature
		fwrite($fh, $zipcontents); 		 				 // package contents, zipped
		fclose($fh);

		// We'll be outputting a chrome extension
		header('Content-type: application/x-chrome-extension');

		// It will be called app.crx
		header('Content-Disposition: attachment; filename="app.crx"');

		// Output the crx file
		readfile($crxfile);

		// Clean up
		unlink($zipfile);
		unlink($crxfile);
		Ajde_FS_Directory::delete($appdir);

		exit;

	}

	private function checkProduction()
	{
		$files = array(
				'phpinfo.php',
				'loadtest.php',
				'install.php'
		);
		$ret = array();
		foreach($files as $file) {
			if (file_exists($file)) {
				$ret[] = array(
						'msg'		=> 'File ' . $file . ' should be deleted in production environment',
						'status'	=> 'warning'
				);
			}
		}
		return $ret;
	}

	private function checkWritable()
	{
		$dirs = array(
			TMP_DIR, LOG_DIR, CACHE_DIR, UPLOAD_DIR
		);
		$ret = array();
		foreach($dirs as $dir) {
			if (!is_writable($dir)) {
				$ret[] = array(
					'msg'		=> 'Directory ' . $dir . ' is not writable',
					'status'	=> 'important'
				);
			}
		}
		return $ret;
	}

    public function log()
    {
        Ajde::app()->getDocument()->setTitle("System log");
        return $this->render();
    }

    public function logpanel()
    {
        $item = $this->getItem();
        $this->getView()->assign('item', $item);
        return $this->render();
    }
}