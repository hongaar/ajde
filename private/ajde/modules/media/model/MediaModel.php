<?php

class MediaModel extends Ajde_Model
{
	protected $_autoloadParents = false;
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
		return $this->getFilename(1024);
	}
	
	public function getTag($width = null, $height = null, $crop = null, $class = null)
	{
		$path = $this->uploadDirectory . $this->thumbnail;
		
		$image = new Ajde_Resource_Image($path);
		$image->setWidth($width);
		$image->setHeight($height);
		$image->setCrop($crop);

		$controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/component:image'));
		$controller->setImage($image);			
		$controller->setExtraClass($class);
		return $controller->invoke();
	}
	
	public function getFilename($width = null, $height = null, $crop = null, $class = null)
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
}
