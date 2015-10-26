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

    public static $_thumbDir = '.thumbnails';

    const ORIENTATION_PORTRAIT = 'portrait';
    const ORIENTATION_LANDSCAPE = 'landscape';

    public function __construct($file)
    {
        if (file_exists($file)) {
            $this->_source = $file;
        } else {
            $this->_source = MEDIA_DIR . 'notfound.png';
        }
        $this->_type = $this->extension();
    }

    public function getUrl($width = null, $height = null, $crop = false)
    {
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setCrop($crop);

        return $this->getLinkUrl();
    }

    public function getLinkUrl()
    {
        // Double url encoding because of mod_rewrite url decoding bug
        // @see http://www.php.net/manual/en/reserved.variables.php#84025
        $url = '_core/component:image/' . urlencode(urlencode($this->getFingerprint())) . '.data';

        if (Config::get('debug') === true) {
            $url .= '&file=' . str_replace(['%2F', '%5C'], ':', urlencode($this->_source));
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
        $w = $this->hasWidth() ? $this->getWidth(false) : null;
        $h = $this->hasHeight() ? $this->getHeight(false) : null;
        $array = ['s' => $this->_source, 'w' => $w, 'h' => $h, 'c' => $this->getCrop()];

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
                case "jpeg":
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

        return ['_source', '_type', '_data'];
    }

    public function __wakeup()
    {
        //$this->_image = $this->getImageResource();
    }

    public function getImageResource()
    {
        // try to allocate a lot of memory
        ini_set('memory_limit', '150M');

        if (!isset($this->_image)) {
            switch ($this->_type) {
                case "jpg":
                case "jpeg":
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

    public function getCalculatedDim()
    {
        return [
            'width' => imageSX($this->getImageResource()),
            'height' => imageSY($this->getImageResource())
        ];
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

    public function getOriginalFilename()
    {
        return $this->_source;
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

        // see if we have a thumb folder
        $thumbPath = $this->dir() . '/' . self::$_thumbDir;
        if (!is_dir($thumbPath)) {
            @mkdir($thumbPath);
        }

        $filename = $this->dir();
        $filename .= '/' . self::$_thumbDir . '/';
        $filename .= $this->filename();
        $filename .= '_' . $width . 'x' . $height;
        $filename .= ($crop ? 'c' : '');
        $filename .= '.';
        $filename .= $this->extension();

        return $filename;
    }

    public function getOrientation()
    {
        return ($this->getHeight() >= $this->getWidth()) ? self::ORIENTATION_PORTRAIT : self::ORIENTATION_LANDSCAPE;
    }

    public function getHeight($calculate = true)
    {
        if ($this->has('height') && !$this->isEmpty('height')) {
            return $this->get('height');
        } else {
            if ($calculate === true) {
                $old_y = imageSY($this->getImageResource());
                if ($this->has('width') && !$this->isEmpty('width')) {
                    $old_x = imageSX($this->getImageResource());

                    return (int)(($old_y / $old_x) * $this->get('width'));
                } else {
                    return $old_y;
                }
            }
        }

        return 0;
    }

    public function getWidth($calculate = true)
    {
        if ($this->has('width') && !$this->isEmpty('width')) {
            return $this->get('width');
        } else {
            if ($calculate === true) {
                $old_x = imageSX($this->getImageResource());
                if ($this->has('height') && !$this->isEmpty('height')) {
                    $old_y = imageSY($this->getImageResource());

                    return (int)(($old_x / $old_y) * $this->get('height'));
                } else {
                    return $old_x;
                }
            }
        }

        return 0;
    }

    public function resize($height, $width, $crop = true, $xCorrection = 0, $yCorrection = 0)
    {
        if ($this->imageInCache($width, $height, $crop)) {
            return;
        }

        $oldWidth = imageSX($this->getImageResource());
        $oldHeight = imageSY($this->getImageResource());

        if (empty($height)) {
            $height = (int)(($oldHeight / $oldWidth) * $width);
        }
        if (empty($width)) {
            $width = (int)(($oldWidth / $oldHeight) * $height);
        }

        if ($this->imageInCache($width, $height, $crop)) {
            return;
        }

        $newImage = ImageCreateTrueColor($width, $height);

        $newWidth = $width;
        $newHeight = intval($oldHeight * ($width / $oldWidth));

        $x_offset = 0;
        $y_offset = 0;

        $adjustWidth = (($crop === true) ? ($newHeight < $height) : ($newHeight > $height));
        $offsetSign = -1;

        if ($adjustWidth) {
            $newHeight = $height;
            $newWidth = intval($oldWidth * ($height / $oldHeight));

            // Correct for cropping left / right
            $x_offset = $offsetSign * intval(($newWidth - $width) / 2);
        } else {
            // Correct for cropping top / bottom
            $y_offset = $offsetSign * intval(($newHeight - $height) / 2);
        }

        $x_offset = $x_offset + $xCorrection;
        $y_offset = $y_offset + $yCorrection;

        $this->fastimagecopyresampled($newImage, $this->getImageResource(), $x_offset, $y_offset, 0, 0, $newWidth,
            $newHeight, $oldWidth, $oldHeight, 5);

        $this->_image = $newImage;

        $this->saveCached($width, $height, $crop);
    }

    public function crop($height, $width)
    {
        return $this->resize($height, $width, true);
    }

    public function save($target)
    {
        switch ($this->_type) {
            case "jpg":
            case "jpeg":
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
            case "jpeg":
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

    public function destroy()
    {
        imagedestroy($this->_image);
    }

    protected function extension()
    {
        $path_info = pathinfo($this->_source);

        return strtolower(issetor($path_info['extension']));
    }

    protected function dir()
    {
        $path_info = pathinfo($this->_source);

        return $path_info['dirname'];
    }

    protected function filename()
    {
        $path_info = pathinfo($this->_source);

        return $path_info['filename'];
    }

    protected function fastimagecopyresampled(
        &$dst_image,
        $src_image,
        $dst_x,
        $dst_y,
        $src_x,
        $src_y,
        $dst_w,
        $dst_h,
        $src_w,
        $src_h,
        $quality = 4
    ) {
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

        // work to do?
        if (empty($src_image) || empty($dst_image) || $quality <= 0) {
            return false;
        }

        // This is the resizing/resampling/transparency-preserving magic
        // https://github.com/maxim/smart_resize_image/blob/master/smart_resize_image.function.php
        if ($this->_type == 'png' || $this->_type == 'gif') {

            $transparency = imagecolortransparent($src_image);
            if ($transparency >= 0) {
                $transparent_color = imagecolorsforindex($src_image, $trnprt_indx);
                $transparency = imagecolorallocate($dst_image, $trnprt_color['red'], $trnprt_color['green'],
                    $trnprt_color['blue']);
                imagefill($dst_image, 0, 0, $transparency);
                imagecolortransparent($dst_image, $transparency);
            } elseif ($this->_type == 'png') {
                imagealphablending($dst_image, false);
                $color = imagecolorallocatealpha($dst_image, 0, 0, 0, 127);
                imagefill($dst_image, 0, 0, $color);
                imagesavealpha($dst_image, true);
            }
        }

        // do the resize
        if ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
            $temp = imagecreatetruecolor($dst_w * $quality + 1, $dst_h * $quality + 1);
            imagecopyresized($temp, $src_image, 0, 0, $src_x, $src_y, $dst_w * $quality + 1, $dst_h * $quality + 1,
                $src_w, $src_h);
            imagecopyresampled($dst_image, $temp, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $dst_w * $quality,
                $dst_h * $quality);
            imagedestroy($temp);
        } else {
            imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
        }

        return true;
    }

}
