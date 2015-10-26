<?php

abstract class Ajde_Lang_Proxy_Model extends Ajde_Model
{
    protected $languageField = 'lang';
    protected $languageRootField = 'lang_root';

    public function getLanguageField()
    {
        return $this->languageField;
    }

    public function getLanguageRootField()
    {
        return $this->languageRootField;
    }

    public function getLanguage()
    {
        return $this->get($this->languageField);
    }

    public function isTranslatable()
    {
        $pk = $this->getPK();

        return !empty($pk);
    }

    /**
     *
     * @param string $lang
     * @return Ajde_Lang_Proxy_Acl_Model|boolean
     */
    public function getTranslated($lang)
    {
        $autoTranslate = Ajde_Lang::getInstance()->autoTranslateModels();
        Ajde_Lang::getInstance()->disableAutoTranslationOfModels();

        $modelName = $this->toCamelCase($this->_tableName) . 'Model';
        /* @var $translation Ajde_Lang_Proxy_Model */
        $translation = new $modelName();

        // if this is the root model, search for a translated leaf
        $exist = $translation->loadByFields([
            $this->languageRootField => $this->getPK(),
            $this->languageField => $lang
        ]);
        $exist = $exist & $translation->hasLoaded();

        // if this is a translation, see if the root has the right language
        if (!$exist && $this->has($this->languageRootField)) {
            $exist = $translation->loadByFields([
                $this->getTable()->getPK() => $this->get($this->languageRootField),
                $this->languageField => $lang
            ]);
            $exist = $exist & $translation->hasLoaded();
        }

        // if this is a translation, see if there is another matching translation
        if (!$exist && $this->has($this->languageRootField)) {
            $exist = $translation->loadByFields([
                $this->languageRootField => $this->get($this->languageRootField),
                $this->languageField => $lang
            ]);
            $exist = $exist & $translation->hasLoaded();
        }

        Ajde_Lang::getInstance()->autoTranslateModels($autoTranslate);

        return $exist ? $translation : false;
    }

    /**
     *
     * @param string $lang
     * @return Ajde_Lang_Proxy_Model
     */
    public function getTranslatedLazy($lang)
    {
        $translation = $this->getTranslated($lang);

        return $translation ? $translation : $this;
    }

    /**
     *
     * @return Ajde_Lang_Proxy_Model|boolean
     */
    public function getRootLang()
    {
        if ($this->hasNotEmpty($this->languageRootField)) {
            $this->loadParent($this->languageRootField);
            $translated = $this->get($this->languageRootField);

            return $translated;
        }

        return false;
    }

    public function loadParent($column)
    {
        $return = parent::loadParent($column);

        return $return;
    }

    public function getTranslations()
    {
        $rootLang = $this->getRootLang();
        if (!$rootLang) {
            $rootLang = $this;
        }

        $collection = $this->getCollection();
        /* @var $collection Ajde_Lang_Proxy_Collection */

        $collection->addFilter(new Ajde_Filter_Where($this->languageRootField, Ajde_Filter::FILTER_EQUALS,
            $rootLang->getPK()));

        return $collection;
    }

    protected function _load($sql, $values, $populate = true)
    {
        $return = parent::_load($sql, $values, $populate);
        if (Ajde_Lang::getInstance()->autoTranslateModels() && $return) {
            // get translation
            $lang = Ajde_Lang::getInstance();
            if ($translation = $this->getTranslated($lang->getLang())) {
                /* @var $translation Ajde_Lang_Proxy_Model */
                $this->reset();
                $this->loadFromValues($translation->values());
            }
        }

        return $return;
    }
}
