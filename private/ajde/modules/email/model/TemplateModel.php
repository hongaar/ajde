<?php

class TemplateModel extends Ajde_Model_With_I18n
{
	protected $_autoloadParents = false;
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
     * @return bool|TemplateModel
     */
    public static function getMaster()
    {
        $master = new self();
        if ($master->loadByField('name', 'master')) {
            return $master;
        }
        return false;
    }

    public function getContent()
    {
        if ($this->name != 'master' && $master = $this->getMaster()) {
            $masterContent = $master->getContent();
            return str_replace('%body%', parent::getContent(), $masterContent);
        } else {
            return parent::getContent();
        }
    }

    public function getSubject()
    {
        return parent::getSubject();
    }
}
