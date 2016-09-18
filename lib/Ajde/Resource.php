<?php

abstract class Ajde_Resource extends Ajde_Object_Standard
{
    const TYPE_JAVASCRIPT = 'js';
    const TYPE_STYLESHEET = 'css';

    public function __construct($type)
    {
        $this->setType($type);
    }

    public function __toString()
    {
        return implode(', ', $this->_data);
    }

    abstract public function getFilename();

    abstract protected function getLinkUrl();

    protected static function exist($filename)
    {
        if (is_file(self::realpath($filename))) {
            return true;
        }

        return false;
    }

    protected static function realpath($filename)
    {
        //        dump($filename, realpath(LOCAL_ROOT . $filename));
        return realpath(LOCAL_ROOT.$filename);
    }

    public static function encodeFingerprint($array)
    {
        return self::_urlEncode(serialize($array));
    }

    public static function decodeFingerprint($fingerprint)
    {
        return unserialize(self::_urlDecode($fingerprint));
    }

    public static function _urlDecode($string)
    {
        return base64_decode($string);
    }

    public static function _urlEncode($string)
    {
        return base64_encode($string);
    }

    public static function getLinkTemplateFilename($type, $format = 'null')
    {
        if (Ajde::app()->getDocument()->hasLayout()) {
            $layout = Ajde::app()->getDocument()->getLayout();
        } else {
            $layout = new Ajde_Layout(config('layout.frontend'));
        }
        $format = issetor($format, 'html');

        $dirPrefixPatterns = [
            APP_DIR,
            CORE_DIR,
        ];
        foreach ($dirPrefixPatterns as $dirPrefixPattern) {
            $prefixedLayout = $dirPrefixPattern.LAYOUT_DIR;
            if (self::exist($prefixedLayout.$layout->getName().'/link/'.$type.'.'.$format.'.php')) {
                return $prefixedLayout.$layout->getName().'/link/'.$type.'.'.$format.'.php';
            }
        }

        return false;
    }

    public function getType()
    {
        return $this->get('type');
    }

    public function setPosition($position)
    {
        $this->set('position', $position);
    }

    public function getPosition()
    {
        return $this->get('position');
    }

    protected function _getLinkTemplateFilename()
    {
        $format = $this->hasFormat() ? $this->getFormat() : null;

        return self::getLinkTemplateFilename($this->getType(), $format);
    }

    public function getLinkCode()
    {
        ob_start();

        // variables for use in included link template
        $url = $this->getLinkUrl();
        $arguments = $this->hasArguments() ? $this->getArguments() : '';

        // create temporary resource for link filename
        $linkFilename = $this->_getLinkTemplateFilename();

        // TODO: performance gain?
        // Ajde_Cache::getInstance()->addFile($linkFilename);
        if ($linkFilename) {
            include LOCAL_ROOT.$linkFilename;
        } else {
            throw new Ajde_Exception('Link filename for '.$url.' not found');
        }

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }

    public function getContents()
    {
        ob_start();

        $filename = $this->getFilename();

        Ajde_Cache::getInstance()->addFile($filename);
        if ($this->exist($filename)) {
            include $this->realpath($filename);
        } else {
            throw new Exception("Couldn't find resource ".$filename);
        }

        $contents = ob_get_contents();
        ob_end_clean();

        return $contents;
    }
}
