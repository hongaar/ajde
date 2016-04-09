<?php

class Ajde_Crud_Field_I18n extends Ajde_Crud_Field_Enum
{
    protected $_useSpan = false;

    public function __construct(Ajde_Crud $crud, $fieldOptions)
    {
        parent::__construct($crud, $fieldOptions);
        $this->set('default', config("i18n.default"));
    }

    public function getFieldsToClone()
    {
        return ($this->has('cloneFields') ? (array)$this->get('cloneFields') : []);
    }

    public function getValues()
    {
        $lang  = Ajde_Lang::getInstance();
        $langs = $lang->getAvailableNiceNames();

        return $langs;
    }

    public function getAvailableTranslations()
    {
        $lang  = Ajde_Lang::getInstance();
        $langs = $lang->getAvailableNiceNames();

        $model = $this->_crud->getModel();
        /* @var $model Ajde_Lang_Proxy_Model */
        $translations = $model->getTranslations();

        $translatedLangs = [];
        foreach ($translations as $model) {
            /* @var $model Ajde_Lang_Proxy_Model */
            $modelLanguage = $model->getLanguage();
            if (!empty($modelLanguage)) {
                $translatedLangs[$modelLanguage] = $model;
            }
        }

        foreach ($langs as $key => &$name) {
            $name = [
                'name' => $name,
            ];

            if (array_key_exists($key, $translatedLangs)) {
                $name['model'] = $translatedLangs[$key];
            }
        }

        return $langs;
    }

    public function _getHtmlAttributes()
    {
        $attributes          = [];
        $attributes['class'] = 'lang';

        return $attributes;
    }
}
