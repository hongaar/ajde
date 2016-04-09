<?php
require LIB_DIR . 'Ajde/Resource/Qrcode/lib/phpqrcode.php';

class Ajde_Resource_Qrcode extends Ajde_Resource
{
    protected $_text;

    public function __construct($text)
    {
        $this->_text = $text;
    }

    public function getLinkUrl()
    {
        // Double url encoding because of mod_rewrite url decoding bug
        // @see http://www.php.net/manual/en/reserved.variables.php#84025
        $url = '_core/component:qrcode/' . urlencode(urlencode($this->getFingerprint())) . '.data';

        if (config("app.debug") === true) {
            $url .= '&text=' . urlencode($this->_text);
        }

        return $url;
    }

    public static function fromFingerprint($fingerprint)
    {
        $array = self::decodeFingerprint($fingerprint);
        extract($array);
        $qr = new self($t);

        return $qr;
    }

    public function getFingerprint()
    {
        $array = ['t' => $this->_text];

        return $this->encodeFingerprint($array);
    }

    public function getFilename()
    {
        return false;
    }

    public function write()
    {
        QRcode::png($this->_text, false, QR_ECLEVEL_M, 6 /* = size */, 0 /* = margin */);
    }
}
