<?php

class AdminSystemController extends AdminController
{
    protected $_allowedActions = [
        'chromeApp',
        'chromeAppDownload',
    ];

    public function check()
    {
        Ajde::app()->getDocument()->setTitle('System check');

        $checks = [];

        $checks[] = [
            'msg' => 'Directories writable?',
            'fn'  => 'writable',
        ];
        $checks[] = [
            'msg' => 'Production ready?',
            'fn'  => 'production',
        ];

        $ret = [];

        foreach ($checks as $check) {
            $ret = call_user_func([$this, 'check'.ucfirst($check['fn'])]);
            if (empty($ret)) {
                $ret = [['msg' => 'OK', 'status' => 'success']];
            }
            foreach ($ret as $re) {
                $results[] = [
                    'check'  => $check['msg'],
                    'msg'    => $re['msg'],
                    'status' => $re['status'],
                ];
            }
        }

        $repository = clone Config::getInstance()->repository();
        $hidden = 'md5=';
        $hide = [
            'database.password',
            'security.secret',
            'services.twitter.secret',
            'services.facebook.secret',
            'services.google.secret',
            'services.soundcloud.secret',
            'shop.transaction.wedeal.password',
            'shop.transaction.wedeal.callbackPassword',
            'shop.transaction.mollie.liveKey',
        ];
        foreach ($hide as $field) {
            if ($repository->has($field)) {
                $repository->set($field, $hidden.md5($repository->get($field)));
            }
        }

        $config = $repository->values();
        $this->getView()->assign('results', $results);
        $this->getView()->assign('config', $config);

        return $this->render();
    }

    public function updateHtml()
    {
        Ajde::app()->getDocument()->setTitle('Ajde updater');

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
            } catch (Exception $e) {
                Ajde_Exception_Log::logException($e);
                $status = $e->getMessage();
            }
        }

        return ['status' => $status];
    }

    public function chromeApp()
    {
        Ajde::app()->getDocument()->setTitle('Chrome app');

        return $this->render();
    }

    // @see http://stackoverflow.com/a/5586372/938297
    public function chromeAppDownload()
    {
        // Temp dir
        $appdir = TMP_DIR.'app'.DIRECTORY_SEPARATOR;
        mkdir($appdir);

        // manifest.json
        $url = config('i18n.rootUrl').'admin/?chromeapp=1';
        $manifest = (object) [
            'manifest_version' => 2,
            'name'             => config('app.title'),
            'description'      => config('app.description'),
            'version'          => '1.0',
            'icons'            => (object) ['128' => 'app.png'],
            'app'              => (object) [
                'urls'   => [$url],
                'launch' => (object) ['web_url' => $url],
            ],
            'permissions'      => ['unlimitedStorage', 'notifications'],
        ];
        $json = json_encode($manifest);

        // Clean temp dir
        Ajde_Fs_Directory::truncate($appdir);

        // Put files
        file_put_contents($appdir.'manifest.json', $json);
        copy(MEDIA_DIR.'app.png', $appdir.'app.png');

        // All files to zip
        $appfiles = Ajde_Fs_Find::findFiles($appdir, '*');

        // Make the zip file
        $zipfile = TMP_DIR.'app.zip';
        $zip = new ZipArchive();
        $zip->open($zipfile, ZIPARCHIVE::OVERWRITE);
        foreach ($appfiles as $file) {
            $zip->addFile($file, basename($file));
        }
        $zip->close();

        // Get contents
        $zipcontents = file_get_contents($zipfile);

        // Path to crx file
        $crxfile = TMP_DIR.'app.crx';

        // Path to pem file
        $pemfile = DEV_DIR.'app.pem';
        $pemcontents = file_get_contents($pemfile);

        // Fetch private key from file and ready it
        $privkey = openssl_get_privatekey($pemcontents);

        // Get public key
        $pubkey = openssl_pkey_get_details($privkey);
        $pubkey = $pubkey['key'];

        // geting rid of -----BEGIN/END PUBLIC KEY-----
        $pubkey = explode('-----', $pubkey);
        $pubkey = trim($pubkey[2]);

        // decode the public key
        $pubkey = base64_decode($pubkey);

        // make a SHA1 signature using our private key
        openssl_sign($zipcontents, $signature, $privkey, OPENSSL_ALGO_SHA1);
        openssl_free_key($privkey);

        // .crx package format:
        //
        //   magic number               char(4)
        //   crx format ver             byte(4)
        //   pub key lenth              byte(4)
        //   signature length           byte(4)
        //   public key                 string
        //   signature                  string
        //   package contents, zipped   string
        //
        // see http://code.google.com/chrome/extensions/crx.html
        //
        $fh = fopen($crxfile, 'wb');
        fwrite($fh, 'Cr24');                             // extension file magic number
        fwrite($fh, pack('V', 2));                       // crx format version
        fwrite($fh, pack('V', strlen($pubkey)));         // public key length
        fwrite($fh, pack('V', strlen($signature)));      // signature length
        fwrite($fh, $pubkey);                            // public key
        fwrite($fh, $signature);                         // signature
        fwrite($fh, $zipcontents);                         // package contents, zipped
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
        Ajde_Fs_Directory::delete($appdir);

        exit;
    }

    private function checkProduction()
    {
        $files = [
            'phpinfo.php',
            'loadtest.php',
            'install.php',
        ];
        $ret = [];
        foreach ($files as $file) {
            if (file_exists(LOCAL_ROOT.$file)) {
                $ret[] = [
                    'msg'    => 'File '.$file.' should be deleted in production environment',
                    'status' => 'warning',
                ];
            }
        }

        return $ret;
    }

    private function checkWritable()
    {
        $dirs = [
            TMP_DIR,
            LOG_DIR,
            CACHE_DIR,
            UPLOAD_DIR,
        ];
        $ret = [];
        foreach ($dirs as $dir) {
            if (!is_writable(LOCAL_ROOT.$dir)) {
                $ret[] = [
                    'msg'    => 'Directory '.$dir.' is not writable',
                    'status' => 'important',
                ];
            }
        }

        return $ret;
    }

    public function log()
    {
        Ajde::app()->getDocument()->setTitle('System log');

        return $this->render();
    }

    public function logpanel()
    {
        $item = $this->getItem();
        $this->getView()->assign('item', $item);

        return $this->render();
    }

    private function _unused()
    {
        $used = new MediaCollection();
        $unused = new MediaCollection();
        $unused->addFilter(new Ajde_Filter_Where('id', Ajde_Filter::FILTER_EQUALS, '-9999'));

        $db = Ajde_Db::getInstance()->getConnection();

        /** @var MediaModel $media */
        foreach ($used as $media) {
            $stmt = $db->query('SELECT id FROM node WHERE media = '.$media->getPK());
            $stmt->execute();
            $node = $stmt->rowCount();

            $stmt = $db->query('SELECT id FROM node_media WHERE media = '.$media->getPK());
            $stmt->execute();
            $nodeMedia = $stmt->rowCount();

            $meta = 0;
            $stmt = $db->query('SELECT * FROM node_meta INNER JOIN meta ON meta.id = node_meta.meta AND node_meta.`value` <> \'\' AND meta.type = \'media\' AND node_meta.`value` = '.$media->getPK());
            $stmt->execute();
            $meta += $stmt->rowCount();
            $stmt = $db->query('SELECT * FROM setting_meta INNER JOIN meta ON meta.id = setting_meta.meta AND setting_meta.`value` <> \'\' AND meta.type = \'media\' AND setting_meta.`value` = '.$media->getPK());
            $stmt->execute();
            $meta += $stmt->rowCount();

            if ($node == 0 && $nodeMedia == 0 && $meta == 0) {
                $unused->add($media);
            }
        }

        return $unused;
    }

    private function _tobecleaned()
    {
        $allMedia = new MediaCollection();
        $toBeCleaned = Ajde_Fs_Find::findFilenames(UPLOAD_DIR, '*.*');

        foreach ($allMedia as $media) {
            if (($index = array_search($media->pointer, $toBeCleaned)) !== false) {
                unset($toBeCleaned[$index]);
            }

            if (($index = array_search($media->thumbnail, $toBeCleaned)) !== false) {
                unset($toBeCleaned[$index]);
            }
        }

        return $toBeCleaned;
    }

    public function cleanuploads()
    {
        $toBeCleaned = $this->_tobecleaned();

        $unused = $this->_unused();

        Ajde::app()->getDocument()->setTitle('Clean uploads');
        $this->getView()->assign('cleaning', $toBeCleaned);
        $this->getView()->assign('unused', $unused);

        return $this->render();
    }

    public function doCleanuploads()
    {
        $toBeCleaned = $this->_tobecleaned();

        foreach ($toBeCleaned as $file) {
            unlink(LOCAL_ROOT.UPLOAD_DIR.$file);
        }

        Ajde_Session_Flash::alert('Orphan files cleaned');

        return $this->redirect(Ajde_Http_Response::REDIRECT_REFFERER);
    }

    public function doDeleteunused()
    {
        $unused = $this->_unused();
        $unused->deleteAll();

        Ajde_Session_Flash::alert('Unused media deleted');

        return $this->redirect(Ajde_Http_Response::REDIRECT_REFFERER);
    }

    public function doCleanthumbs()
    {
        $toBeCleaned = Ajde_Fs_Find::findFilenames(UPLOAD_DIR.Ajde_Resource_Image::$_thumbDir.DIRECTORY_SEPARATOR,
            '*.*');

        foreach ($toBeCleaned as $file) {
            unlink(LOCAL_ROOT.UPLOAD_DIR.Ajde_Resource_Image::$_thumbDir.DIRECTORY_SEPARATOR.$file);
        }

        Ajde_Session_Flash::alert('Thumbnails will be refreshed next time they are loaded');

        return $this->redirect(Ajde_Http_Response::REDIRECT_REFFERER);
    }
}
