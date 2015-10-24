<?php

abstract class Ajde_Resource_Local_Compressor extends Ajde_Object_Standard
{
	const CACHE_STATUS_NOT_EXIST = 0;
	const CACHE_STATUS_EXIST = 1;
	const CACHE_STATUS_UPDATE = 2;

	protected $_resources = array();
	protected $_contents;

	public function  __construct()
	{
		$this->setBase(CACHE_DIR);
	}

	public static function fromType($type)
	{
		$className = __CLASS__ . '_' . ucfirst($type);
		if (!Ajde_Core_Autoloader::exists($className))
		{
			throw new Ajde_Exception(sprintf("Compressor for type %s not found", $type), 90017);
		}
		return new $className();
	}

	/**
	 *
	 * @param array $resources Array of Ajde_Resource_Local
	 */
	public function setResources($resources)
	{
		if (is_array($resources))
		{
			foreach($resources as $resource)
			{
				$this->addResource($resource);
			}
		}
	}

	/**
	 *
	 * @param Ajde_Resource_Local $resource 
	 */
	public function addResource(Ajde_Resource_Local $resource)
	{
		$this->_resources[] = $resource;
	}

	public function getBase()
	{
		return $this->get('base');
	}

	public function setBase($base)
	{
		$this->set('base', $base);
	}

	public function getType()
	{
		return $this->get('type');
	}

	public function setType($type)
	{
		return $this->set('type', $type);
	}

	public function getFilename()
	{
		$hash = $this->getHash();
		return $this->getBase() . $hash['fileName'] . '.' . $hash['fileTime'] . '.' . $this->getType();
	}

	public function setFilename($filename)
	{
		$this->set('filename', $filename);
	}

	public function getHash()
	{
		if (!$this->has('hash'))
		{
			$fileTimeContext = hash_init('md5');
			$fileNameContext = hash_init('md5');
			foreach($this->_resources as $resource)
			{
				/* @var $resource Ajde_Resource_Local */
				hash_update($fileTimeContext, filemtime($resource->getFilename()));
				hash_update($fileNameContext, $resource->getFilename());
			}
			$this->setHash(array(
				'fileTime' => hash_final($fileTimeContext),
				'fileName' => hash_final($fileNameContext)
			));
		}
		return $this->get('hash');
	}

	public function setHash($hash)
	{
		$this->set('hash', $hash);
	}

	public function getCacheStatus()
	{
		$hash = $this->getHash();
		$fileTimePattern = $hash['fileName'] . '.' . $hash['fileTime'] . '.' . $this->getType();
		if ($fileName = Ajde_FS_Find::findFile($this->getBase(), $fileTimePattern))
		{
			return array('status' => self::CACHE_STATUS_EXIST, 'fileName' => $fileName);
		}
		$fileNamePattern = $hash['fileName'] . '.*.' . $this->getType();
		if ($fileName = Ajde_FS_Find::findFile($this->getBase(), $fileNamePattern))
		{
			return array('status' => self::CACHE_STATUS_UPDATE, 'fileName' => $fileName);
		}
		return array('status' => self::CACHE_STATUS_NOT_EXIST, 'fileName' => '');
	}

	/**
	 * 
	 * @return Ajde_Resource_Local_Compressed
	 */
	public function process()
	{
		$cacheStatus = $this->getCacheStatus();
		switch ($cacheStatus['status'])
		{
			case self::CACHE_STATUS_UPDATE:
				unlink($cacheStatus['fileName']);
			case self::CACHE_STATUS_NOT_EXIST:
				$this->saveCache();
				break;
			case self::CACHE_STATUS_EXIST:
				break;
		}
		return new Ajde_Resource_Local_Compressed($this->getType(), $this->getFilename());
	}

	public function saveCache()
	{
		// Bind document processors to compressor
		Ajde_Document::registerDocumentProcessor($this->getType(), 'compressor');
		
		// Prepare content
		$this->_contents = '';
		foreach($this->_resources as $resource)
		{
			/* @var $resource Ajde_Resource_Local */
			$this->_contents .= $resource->getContents() . PHP_EOL;
		}
		if (!is_writable($this->getBase()))
		{
			throw new Ajde_Exception(sprintf("Directory %s is not writable", $this->getBase()), 90014);
		}
		
		// Execute compression
		Ajde_Event::trigger($this, 'beforeCompress');
		$this->compress();
		Ajde_Event::trigger($this, 'afterCompress');
		
		// Save file to cache folder
		file_put_contents($this->getFilename(), $this->_contents);
	}
	
	public function getContents()
	{
		return $this->_contents;
	}
	
	public function setContents($contents)
	{
		$this->_contents = $contents;
	}

	abstract public function compress();

}