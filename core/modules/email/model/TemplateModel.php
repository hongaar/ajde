<?php

class TemplateModel extends Ajde_Model_With_I18n
{
    protected $_autoloadParents = true;
    protected $_displayField    = 'name';

    protected static $_cssInliner = 'emogrifier'; // one of passthrough|emogrifier|torchbox

    public function displayLang()
    {
        Ajde::app()->getDocument()->getLayout()->getParser()->getHelper()->requireCssPublic('core/flags.css');

        $lang        = Ajde_Lang::getInstance();
        $currentLang = $this->get('lang');
        if ($currentLang) {
            $image = '<img src="" class="flag flag-' . strtolower(substr($currentLang, 3,
                    2)) . '" alt="' . $currentLang . '" />';

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

    public function getContent($markup = '', $inlineCss = true)
    {
        $content = $markup . parent::getContent();
        $master  = $this->getMaster()->hasLoaded() ? $this->getMaster() : false;
        $style   = $this->hasNotEmpty('style') ? $this->getStyle() : false;

        if ($style) {
            $stylesheet = ASSETS_DIR . 'css' . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'email' . DIRECTORY_SEPARATOR . $style . '.css';
            if (!is_file(LOCAL_ROOT . $stylesheet)) {
                throw new Ajde_Exception('Stylesheet ' . $stylesheet . ' not found');
            }
            $stylesheetContent = file_get_contents(LOCAL_ROOT . $stylesheet);
            $content           = "<html><body><style>" . $stylesheetContent . "</style>" . $content . "</body></html>";
        }

        if ($master) {
            $masterContent = $master->getContent('', false);
            $content       = str_replace('%body%', $content, $masterContent);
        }

        if ($inlineCss && ($style || ($master && $master->getStyle()))) {
            $content = html_entity_decode(self::applyCssInliner($content));
        }

        return $content;
    }

    public static function applyCssInliner($html)
    {
        /** @var Ajde_Mailer_CssInliner_CssInlinerInterface $inliner */
        $inlinerClass = 'Ajde_Mailer_CssInliner_' . ucfirst(self::$_cssInliner);

        return call_user_func($inlinerClass . '::inlineCss', $html);
    }

    /**
     * @param $html
     * @return string
     * @throws Exception
     * @deprecated Use applyCssInliner instead
     */
    public static function inlineCss($html)
    {
        throw new Ajde_Core_Exception_Deprecated('Use applyCssInliner instead');
    }

    public function getSubject()
    {
        return parent::getSubject();
    }
}
