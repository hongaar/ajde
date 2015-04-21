<?php

class MediaModel extends Ajde_Model
{
	protected $_autoloadParents = true;
	protected $_displayField = 'name';
	
	protected $uploadDirectory = UPLOAD_DIR;
    protected $replaceOldFile = false;
	
	public function beforeInsert()
    {
		// Download thumbnails
        $this->saveFileFromWeb();

		// Added
		$this->added = new Ajde_Db_Function("NOW()");

		// Sort
		$collection = new MediaCollection();
		$min = 999;
		foreach($collection as $item) {
			$min = ($item->sort < $min) ? $item->sort : $min;
		}
		$this->sort = $min - 1;
    }

    public function beforeSave()
	{
		$this->saveFileFromWeb();
	}

    public function afterDelete()
    {
        // TODO: Delete files?
        // dump($this);
    }
	
	/**
	 *
	 * @return MediatypeModel
	 */
	public function getMediatype()
	{
		$this->loadParent('mediatype');
		return parent::getMediatype();
	}
	
	public function getPath()
	{
		return $this->type == 'image' ? $this->getFilename(1024) : $this->uploadDirectory . $this->pointer;
	}

    /**
     * @return Ajde_Resource_Image
     */
    public function getResource()
    {
        $path = $this->uploadDirectory . $this->thumbnail;
        return new Ajde_Resource_Image($path);
    }
	
	public function getTag($width = null, $height = null, $crop = null, $class = null, $attributes = array(), $lazy = false)
	{
		$path = $this->uploadDirectory . $this->thumbnail;
		
		$image = new Ajde_Resource_Image($path);
		$image->setWidth($width);
		$image->setHeight($height);
		$image->setCrop($crop);

		$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/component:image'));
		$controller->setImage($image);			
		$controller->setExtraClass($class);
        $controller->setAttributes($attributes);
        $controller->setLazy($lazy);
		return $controller->invoke();
	}

    public function getLazyTag($width = null, $height = null, $crop = null, $class = null, $attributes = array())
    {
        return $this->getTag($width, $height, $crop, $class, $attributes, true);
    }
	
	public function getFilename($width = null, $height = null, $crop = null)
	{
		$path = $this->uploadDirectory . $this->thumbnail;
		
		$image = new Ajde_Resource_Image($path);
		$image->setWidth($width);
		$image->setHeight($height);
		$image->setCrop($crop);
		
		return $image->getLinkUrl();
	}
	
	public function getExtension()
	{
		return pathinfo($this->thumbnail, PATHINFO_EXTENSION);
	}
	
	public function getAbsoluteUrl()
	{
		return Config::get('site_root') . $this->uploadDirectory . $this->thumbnail;
	}
	
	private function saveFileFromWeb()
    {
        if ($this->has('thumbnail') &&
                (substr(strtolower($this->thumbnail), 0, 7) === 'http://' ||
                substr(strtolower($this->thumbnail), 0, 8) === 'https://')) {

            $image = Ajde_Http_Curl::get($this->thumbnail);

            $basename = basename(parse_url($this->thumbnail, PHP_URL_PATH));
            $filename = pathinfo($basename, PATHINFO_FILENAME);
            $ext = pathinfo($basename, PATHINFO_EXTENSION);

            if (strtolower($ext) === 'jpeg') {
                $ext = 'jpg';
            }
			
			if(!$this->replaceOldFile){
                // don't overwrite previous files that were uploaded
                while (is_file($this->uploadDirectory . $filename . '.' . $ext)) {
                    $filename .= rand(10, 99);
                }
            }

            $path = $this->uploadDirectory . $filename . '.' . $ext;

            $fh = fopen($path, 'wb');
            fwrite($fh, $image);
            fclose($fh);

            $this->thumbnail = $filename . '.' . $ext;
        }
    }

    public function saveFileFromDrive($filename, $oauthToken)
    {
        if ($filename && $oauthToken && $this->has('pointer') &&
            (substr(strtolower($this->pointer), 0, 24) === 'https://drive.google.com')) {

            $basename = basename(parse_url($filename, PHP_URL_PATH));
            $filename = pathinfo($basename, PATHINFO_FILENAME);
            $ext = pathinfo($basename, PATHINFO_EXTENSION);

            if (strtolower($ext) === 'jpeg') {
                $ext = 'jpg';
            }

            if(!$this->replaceOldFile){
                // don't overwrite previous files that were uploaded
                while (is_file($this->uploadDirectory . $filename . '.' . $ext)) {
                    $filename .= rand(10, 99);
                }
            }

            $path = $this->uploadDirectory . $filename . '.' . $ext;

            $curlResult = Ajde_Http_Curl::get($this->pointer, $path, array(
                'Authorization: Bearer ' . $oauthToken
            ));

//            $curlResult = Ajde_Http_Curl::get($this->pointer, $path);

            if (!$curlResult) {
                return 'error';
            }

            $this->name = $filename;
            $this->pointer = $filename . '.' . $ext;
            $this->thumbnail = $this->pointer;

            return true;
        }

        return false;
    }

    public function displayType()
    {
        $extension = pathinfo($this->pointer, PATHINFO_EXTENSION);
        if ($this->getType() == 'embed') $extension = 'mpg';
        return "<img class='icon' src='" . Ajde_Resource_FileIcon::_($extension) . "'' />";
    }
}
