<?php

/*
 * Code from CambioCMS project
 * @link http://code.google.com/p/cambiocms/source/browse/trunk/cms/includes/602_html.image.php
 */ 

class Ajde_Resource_Image extends Ajde_Resource
{
	protected $_cache;
	protected $_source;
	protected $_type;
	protected $_image;
	
	public function __construct($file)
	{
		$this->_source = $file;
		$this->_type = $this->extension();
	}
	
	public function getLinkUrl()
	{
		// Double url encoding because of mod_rewrite url decoding bug
		// @see http://www.php.net/manual/en/reserved.variables.php#84025
		$url = '_core/component:image/' . urlencode(urlencode($this->getFingerprint())) . '.data';

		if (Config::get('debug') === true)
		{
			$url .= '&file=' . str_replace('%2F', ':', urlencode($this->_source));
		}
		return $url;
	}
	
	public static function fromFingerprint($fingerprint)
	{
		$array = self::decodeFingerprint($fingerprint);
		extract($array);
		$image = new Ajde_Resource_Image($s);
		$image->setWidth($w);
		$image->setHeight($h);
		$image->setCrop($c);
		return $image;
	}
	
	public function getFingerprint()
	{
		$w = $this->hasWidth() ? $this->getWidth() : null;
		$h = $this->hasHeight() ? $this->getHeight() : null;
		$array = array('s' => $this->_source, 'w' => $w, 'h' => $h, 'c' => $this->getCrop());
		return $this->encodeFingerprint($array);
	}
	
	public function getContents()
	{
		return $this->getImage();
	}
	
	public function getImage()
	{
		if (isset($this->_cache)) {
			$image = file_get_contents($this->_cache);
		} else {
			ob_start();
			switch ($this->_type) {
				case "jpg": 
					imagejpeg($this->getImageResource());
					break;
				case "png":
					imagepng($this->getImageResource());
					break;
				case "gif":
					imagegif($this->getImageResource());
					break;
			}
			$image = ob_get_contents();
			ob_end_clean();
		}
		return $image;
	}
	
	public function __sleep()
	{
		$this->_image = null;
		return array('_source', '_type', '_data');
	}

	public function __wakeup()
	{
		//$this->_image = $this->getImageResource();
	}
	
	public function getImageResource()
	{
		if (!isset($this->_image)) {
			switch ($this->_type) {
				case "jpg": 
					$this->_image = imagecreatefromjpeg($this->_source);
					break;
				case "png":
					$this->_image = imagecreatefrompng($this->_source);
					break;
				case "gif":
					$this->_image = imagecreatefromgif($this->_source);
					break;
			}
		}
		return $this->_image;
	}
	
	public function resize($dim, $w_or_h) {
		
		$old_x=imageSX($this->getImageResource());
		$old_y=imageSY($this->getImageResource());
			
		if ($w_or_h = "w") {
			$thumb_w=$new_w;
			$thumb_h=$old_y*($new_w/$old_x);
		}
		if ($w_or_h = "h") {
			$thumb_w=$old_x*($new_h/$old_y);
			$thumb_h=$new_h;
		}
		
		$newimage = ImageCreateTrueColor($thumb_w,$thumb_h);
		
		$this->fastimagecopyresampled($newimage,$this->getImageResource(),0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y,5);
		
		$this->_image = $newimage;		
	}
	
	public function getCalculatedDim()
	{
		return array(
			'width' => imageSX($this->getImageResource()),
			'height' => imageSY($this->getImageResource())
		);
	}
	
	public function imageInCache($width, $height, $crop = true)
	{
		if (is_file($cache = $this->getGeneratedFilename($width, $height, $crop))) {
			$this->_cache = $cache;
			return true;
		}
		return false;
	}
	
	public function saveCached($width, $height, $crop = true)
	{
		$this->save($this->getGeneratedFilename($width, $height, $crop));
	}
	
	public function getFilename()
	{
		return $this->getGeneratedFilename();
	}
	
	public function getGeneratedFilename($width = null, $height = null, $crop = null)
	{
		if (
				!isset($width) && !$this->hasWidth() &&
				!isset($height) && !$this->hasHeight() &&
				!isset($crop) && !$this->hasCrop()
			) {
			return $this->_source;
		}
		
		if (!isset($width) && $this->hasWidth()) {
			$width = $this->getWidth();
		}
		if (!isset($height) && $this->hasHeight()) {
			$height = $this->getHeight();
		}
		if (!isset($crop) && $this->hasCrop()) {
			$crop = $this->getCrop();
		} elseif (!isset($crop)) {
			$crop = true;
		}
		
		$filename = $this->dir();
		$filename .= '/';
		$filename .= $this->filename();
		$filename .= '_' . $width . 'x' . $height;
		$filename .= ($crop ? 'c' : '');
		$filename .= '.';
		$filename .= $this->extension();
		return $filename;
	}
	
	public function getHeight()
	{
		if ($this->has('height') && !$this->isEmpty('height')) {
			return $this->get('height');
		} else {						
			$old_y=imageSY($this->getImageResource());
			if ($this->has('width') && !$this->isEmpty('width')) {
				$old_x=imageSX($this->getImageResource());
				return (int) (($old_y / $old_x) * $this->get('width'));
			} else {
				return $old_y;				
			}
		}
	}
	
	public function getWidth()
	{
		if ($this->has('width') && !$this->isEmpty('width')) {
			return $this->get('width');
		} else {
			$old_x=imageSX($this->getImageResource());			
			if ($this->has('height') && !$this->isEmpty('height')) {
				$old_y=imageSY($this->getImageResource());
				return (int) (($old_x / $old_y) * $this->get('height'));
			} else {
				return $old_x;				
			}
		}
	}
	
	public function crop($height, $width)
	{
		if ($this->imageInCache($width, $height, true)) {
			return;
		}
		
		$old_x=imageSX($this->getImageResource());
		$old_y=imageSY($this->getImageResource());
				
		if (empty($height)) {
			$height = (int) (($old_y / $old_x) * $width);
		}
		if (empty($width)) {
			$width = (int) (($old_x / $old_y) * $height);
		}
		
		if ($this->imageInCache($width, $height, true)) {
			return;
		}
		
		// no x or y correction
		$x_o = 0; //intval($_GET["x"]);
		$y_o = 0; //intval($_GET["y"]);
		
		$newimage=ImageCreateTrueColor($width,$height);
				
		$thumb_w=$width;
		$thumb_h=intval($old_y*($width/$old_x));
		
		$x_offset = 0;
		$y_offset = 0;
		
		if ($thumb_h < $height) {
			$thumb_h=$height;
			$thumb_w=intval($old_x*($height/$old_y));
				
			// hoogte kleiner dan breedte
			$x_offset = intval(($thumb_w - $width) / 2);
			//$x_offset = $x_offset * 2;
						
		} else {
			
			// hoogte groter
			$y_offset = ($thumb_h - $height) / 2;
			//$y_offset = $y_offset * 2;
			
		}
		
		$x_offset = $x_offset + $x_o;
		$y_offset = $y_offset + $y_o;
		
		$this->fastimagecopyresampled($newimage,$this->getImageResource(),-$x_offset,-$y_offset,0,0,$thumb_w,$thumb_h,$old_x,$old_y,5);
		
		$this->_image = $newimage;
		
		$this->saveCached($width, $height, true);
	}
	
	public function save($target)
	{
		switch ($this->_type) {
			case "jpg": 
				imagejpeg($this->_image, $target);
				break;
			case "png":
				imagepng($this->_image, $target);
				break;
			case "gif":
				imagegif($this->_image, $target);
				break;
		}
	}
	
	public function getMimeType()
	{
		switch ($this->_type) {
			case "jpg": 
				return "image/jpeg";
				break;
			case "png":
				return "image/png";
				break;
			case "gif":
				return "image/gif";
				break;
		}
	}
	
	public function getBase64()
	{
		return 'data:' . $this->getMimeType() . ';base64,' . base64_encode($this->getImage());
	}
	
	public function destroy() {
		imagedestroy($this->_image); 
	}
	
	protected function extension() {
	    $path_info = pathinfo($this->_source);		
	    return strtolower($path_info['extension']);
	}
	
	protected function dir() {
		$path_info = pathinfo($this->_source);
	    return $path_info['dirname'];
	}
	
	protected function filename() {
		$path_info = pathinfo($this->_source);
	    return $path_info['filename'];
	}
	
	protected function fastimagecopyresampled (&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3) {
		// Plug-and-Play fastimagecopyresampled function replaces much slower imagecopyresampled.
		// Just include this function and change all "imagecopyresampled" references to "fastimagecopyresampled".
		// Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
		// Author: Tim Eckel - Date: 09/07/07 - Version: 1.1 - Project: FreeRingers.net - Freely distributable - These comments must remain.
		//
		// Optional "quality" parameter (defaults is 3). Fractional values are allowed, for example 1.5. Must be greater than zero.
		// Between 0 and 1 = Fast, but mosaic results, closer to 0 increases the mosaic effect.
		// 1 = Up to 350 times faster. Poor results, looks very similar to imagecopyresized.
		// 2 = Up to 95 times faster.  Images appear a little sharp, some prefer this over a quality of 3.
		// 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled, just faster.
		// 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
		// 5 = No speedup. Just uses imagecopyresampled, no advantage over imagecopyresampled.
		
		if (empty($src_image) || empty($dst_image) || $quality <= 0) { return false; }
		if ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
		$temp = imagecreatetruecolor ($dst_w * $quality + 1, $dst_h * $quality + 1);
		imagecopyresized ($temp, $src_image, 0, 0, $src_x, $src_y, $dst_w * $quality + 1, $dst_h * $quality + 1, $src_w, $src_h);
		imagecopyresampled ($dst_image, $temp, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $dst_w * $quality, $dst_h * $quality);
		imagedestroy ($temp);
		} else imagecopyresampled ($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
		return true;
	}
	
}