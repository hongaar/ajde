<?php

class TemplateModel extends Ajde_Model_With_I18n
{
	protected $_autoloadParents = true;
	protected $_displayField = 'name';

    public function displayLang()
    {
        Ajde::app()->getDocument()->getLayout()->getParser()->getHelper()->requireCssPublic('core/flags.css');

        $lang = Ajde_Lang::getInstance();
        $currentLang = $this->get('lang');
        if ($currentLang) {
            $image = '<img src="" class="flag flag-' . strtolower(substr($currentLang, 3, 2)) . '" alt="' . $currentLang . '" />';
            return $image . $lang->getNiceName($currentLang);
        }
        return '';
    }

    /**
     * @return TemplateModel
     */
    public function getMaster()
    {
        return parent::getMaster();
    }

    public function getContent()
    {
        $content = parent::getContent();
        $style = $this->getStyle();

        if ($style) {
            $stylesheet = PUBLIC_DIR . 'css' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'email' . DIRECTORY_SEPARATOR . $style . '.css';
            if (!is_file($stylesheet)) {
                throw new Ajde_Exception('Stylesheet ' . $stylesheet . ' not found');
            }
            $stylesheetContent = file_get_contents($stylesheet);
            $content = "<html><body><style>" . $stylesheetContent . "</style>" . $content . "</body></html>";
        }

        if ($this->getMaster() && $this->getMaster()->hasLoaded()) {
            $masterContent = $this->getMaster()->getContent();
            $content = str_replace('%body%', $content, $masterContent);
        }

        if ($style || ($this->getMaster() && $this->getMaster()->hasLoaded() && $this->getMaster()->getStyle())) {
            $content = html_entity_decode( self::inlineCss($content) );
        }

        return $content;
    }

    public static function inlineCss($html)
    {
        $url = 'http://inlinestyler.torchboxapps.com/styler/convert/';
        $data = array(
            'returnraw' => '1',
            'source' => $html
        );
        return Ajde_Http_Curl::post($url, $data);
    }

    public function getSubject()
    {
        return parent::getSubject();
    }
}
