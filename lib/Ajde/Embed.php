<?php

class Ajde_Embed extends Ajde_Object_Standard
{
    protected $_code;
    protected $_parser;

    protected $_width  = 650;
    protected $_height = 471;

    protected $_allowedTags = '<iframe><embed><object>';

    private static $_detect = [
        'soundcloud' => 'soundcloud.com',
        'vimeo'      => 'vimeo.com',
        'youtube'    => ['youtube.com', 'youtu.be'],
        'mixcloud'   => 'mixcloud.com'
    ];

    /**
     *
     * @param type $code
     * @return Ajde_Embed
     */
    public static function fromCode($code)
    {
        $embedClass = 'Ajde_Embed';
        foreach (self::$_detect as $provider => $test) {
            if (is_array($test)) {
                foreach ($test as $testPart) {
                    if (substr_count($code, $testPart) > 0) {
                        $providerClass = 'Ajde_Embed_' . ucfirst($provider);
                        if (class_exists($providerClass)) {
                            $embedClass = $providerClass;
                        }
                    }
                }
            } else {
                if (substr_count($code, $test) > 0) {
                    $providerClass = 'Ajde_Embed_' . ucfirst($provider);
                    if (class_exists($providerClass)) {
                        $embedClass = $providerClass;
                    }
                }
            }
        }

        return new $embedClass($code);
    }

    public function __construct($code)
    {
        $this->_code = $code;
    }

    public function getProvider()
    {
        $name = str_replace('Ajde_Embed', '', get_class($this));
        if (!empty($name)) {
            return str_replace('_', '', $name);
        } else {
            return false;
        }
    }

    public function setWidth($width)
    {
        $this->_width = $width;
    }

    public function setHeight($height)
    {
        $this->_height = $height;
    }

    protected function _setWidth()
    {
        $ptn         = "/width=(\'|\")([0-9]+)(\'|\")/";
        $rpltxt      = "width='" . $this->_width . "'";
        $this->_code = preg_replace($ptn, $rpltxt, $this->_code);
    }

    protected function _setHeight()
    {
        $ptn         = "/height=(\'|\")([0-9]+)(\'|\")/";
        $rpltxt      = "height='" . $this->_height . "'";
        $this->_code = preg_replace($ptn, $rpltxt, $this->_code);
    }

    public function stripTags()
    {
        $this->_code = strip_tags($this->_code, $this->_allowedTags);
    }

    public function getCode()
    {
        return $this->_code;
    }

    public function getThumbnail()
    {
        return null;
    }
}
