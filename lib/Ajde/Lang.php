<?php

class Ajde_Lang extends Ajde_Object_Singleton
{
    protected $_adapter = null;
    protected $_lang;

    protected $_autoTranslationOfModels = true;

    protected static $_niceNames = [
        'aa' => 'Afar',
        'ab' => 'Abkhaz',
        'ae' => 'Avestan',
        'af' => 'Afrikaans',
        'ak' => 'Akan',
        'am' => 'Amharic',
        'an' => 'Aragonese',
        'ar' => 'Arabic',
        'as' => 'Assamese',
        'av' => 'Avaric',
        'ay' => 'Aymara',
        'az' => 'Azerbaijani',
        'ba' => 'Bashkir',
        'be' => 'Belarusian',
        'bg' => 'Bulgarian',
        'bh' => 'Bihari',
        'bi' => 'Bislama',
        'bm' => 'Bambara',
        'bn' => 'Bengali',
        'bo' => 'Tibetan Standard, Tibetan, Central',
        'br' => 'Breton',
        'bs' => 'Bosnian',
        'ca' => 'Catalan; Valencian',
        'ce' => 'Chechen',
        'ch' => 'Chamorro',
        'co' => 'Corsican',
        'cr' => 'Cree',
        'cs' => 'Czech',
        'cu' => 'Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic',
        'cv' => 'Chuvash',
        'cy' => 'Welsh',
        'da' => 'Danish',
        'de' => 'German',
        'dv' => 'Divehi; Dhivehi; Maldivian;',
        'dz' => 'Dzongkha',
        'ee' => 'Ewe',
        'el' => 'Greek, Modern',
        'en' => 'English',
        'eo' => 'Esperanto',
        'es' => 'Spanish; Castilian',
        'et' => 'Estonian',
        'eu' => 'Basque',
        'fa' => 'Persian',
        'ff' => 'Fula; Fulah; Pulaar; Pular',
        'fi' => 'Finnish',
        'fj' => 'Fijian',
        'fo' => 'Faroese',
        'fr' => 'French',
        'fy' => 'Western Frisian',
        'ga' => 'Irish',
        'gd' => 'Scottish Gaelic; Gaelic',
        'gl' => 'Galician',
        'gn' => 'GuaranÃ­',
        'gu' => 'Gujarati',
        'gv' => 'Manx',
        'ha' => 'Hausa',
        'he' => 'Hebrew (modern)',
        'hi' => 'Hindi',
        'ho' => 'Hiri Motu',
        'hr' => 'Croatian',
        'ht' => 'Haitian; Haitian Creole',
        'hu' => 'Hungarian',
        'hy' => 'Armenian',
        'hz' => 'Herero',
        'ia' => 'Interlingua',
        'id' => 'Indonesian',
        'ie' => 'Interlingue',
        'ig' => 'Igbo',
        'ii' => 'Nuosu',
        'ik' => 'Inupiaq',
        'io' => 'Ido',
        'is' => 'Icelandic',
        'it' => 'Italian',
        'iu' => 'Inuktitut',
        'ja' => 'Japanese (ja)',
        'jv' => 'Javanese (jv)',
        'ka' => 'Georgian',
        'kg' => 'Kongo',
        'ki' => 'Kikuyu, Gikuyu',
        'kj' => 'Kwanyama, Kuanyama',
        'kk' => 'Kazakh',
        'kl' => 'Kalaallisut, Greenlandic',
        'km' => 'Khmer',
        'kn' => 'Kannada',
        'ko' => 'Korean',
        'kr' => 'Kanuri',
        'ks' => 'Kashmiri',
        'ku' => 'Kurdish',
        'kv' => 'Komi',
        'kw' => 'Cornish',
        'ky' => 'Kirghiz, Kyrgyz',
        'la' => 'Latin',
        'lb' => 'Luxembourgish, Letzeburgesch',
        'lg' => 'Luganda',
        'li' => 'Limburgish, Limburgan, Limburger',
        'ln' => 'Lingala',
        'lo' => 'Lao',
        'lt' => 'Lithuanian',
        'lu' => 'Luba-Katanga',
        'lv' => 'Latvian',
        'mg' => 'Malagasy',
        'mh' => 'Marshallese',
        'mi' => 'Maori',
        'mk' => 'Macedonian',
        'ml' => 'Malayalam',
        'mn' => 'Mongolian',
        'mr' => 'Marathi (Mara?hi)',
        'ms' => 'Malay',
        'mt' => 'Maltese',
        'my' => 'Burmese',
        'na' => 'Nauru',
        'nb' => 'Norwegian BokmÃ¥l',
        'nd' => 'North Ndebele',
        'ne' => 'Nepali',
        'ng' => 'Ndonga',
        'nl' => 'Dutch',
        'nn' => 'Norwegian Nynorsk',
        'no' => 'Norwegian',
        'nr' => 'South Ndebele',
        'nv' => 'Navajo, Navaho',
        'ny' => 'Chichewa; Chewa; Nyanja',
        'oc' => 'Occitan',
        'oj' => 'Ojibwe, Ojibwa',
        'om' => 'Oromo',
        'or' => 'Oriya',
        'os' => 'Ossetian, Ossetic',
        'pa' => 'Panjabi, Punjabi',
        'pi' => 'Pali',
        'pl' => 'Polish',
        'ps' => 'Pashto, Pushto',
        'pt' => 'Portuguese',
        'qu' => 'Quechua',
        'rm' => 'Romansh',
        'rn' => 'Kirundi',
        'ro' => 'Romanian, Moldavian, Moldovan',
        'ru' => 'Russian',
        'rw' => 'Kinyarwanda',
        'sa' => 'Sanskrit (Sa?sk?ta)',
        'sc' => 'Sardinian',
        'sd' => 'Sindhi',
        'se' => 'Northern Sami',
        'sg' => 'Sango',
        'si' => 'Sinhala, Sinhalese',
        'sk' => 'Slovak',
        'sl' => 'Slovene',
        'sm' => 'Samoan',
        'sn' => 'Shona',
        'so' => 'Somali',
        'sq' => 'Albanian',
        'sr' => 'Serbian',
        'ss' => 'Swati',
        'st' => 'Southern Sotho',
        'su' => 'Sundanese',
        'sv' => 'Swedish',
        'sw' => 'Swahili',
        'ta' => 'Tamil',
        'te' => 'Telugu',
        'tg' => 'Tajik',
        'th' => 'Thai',
        'ti' => 'Tigrinya',
        'tk' => 'Turkmen',
        'tl' => 'Tagalog',
        'tn' => 'Tswana',
        'to' => 'Tonga (Tonga Islands)',
        'tr' => 'Turkish',
        'ts' => 'Tsonga',
        'tt' => 'Tatar',
        'tw' => 'Twi',
        'ty' => 'Tahitian',
        'ug' => 'Uighur, Uyghur',
        'uk' => 'Ukrainian',
        'ur' => 'Urdu',
        'uz' => 'Uzbek',
        've' => 'Venda',
        'vi' => 'Vietnamese',
        'vo' => 'VolapÃ¼k',
        'wa' => 'Walloon',
        'wo' => 'Wolof',
        'xh' => 'Xhosa',
        'yi' => 'Yiddish',
        'yo' => 'Yoruba',
        'za' => 'Zhuang, Chuang',
        'zh' => 'Chinese',
        'zu' => 'Zulu',
    ];

    /**
     * @return Ajde_Lang
     */
    public static function getInstance()
    {
        static $instance;

        return $instance === null ? $instance = new self : $instance;
    }

    protected function __construct()
    {
        $this->setLang($this->detect());
    }

    public static function trans($ident, $module = null)
    {
        return self::getInstance()->translate($ident, $module);
    }

    public function getLang()
    {
        return $this->_lang;
    }

    public function getShortLang($lang = null)
    {
        return $lang ? substr($lang, 0, 2) : substr($this->_lang, 0, 2);
    }

    public function getAlternateUrl($lang = null)
    {
        $routeLang  = $this->getShortLang();
        $currentUrl = preg_replace('/^' . $routeLang . '\/?/', '', Ajde::app()->getRoute()->getOriginalRoute());

        return config("app.rootUrl") . $this->getShortLang($lang) . '/' . $currentUrl;
    }

    public function setLang($lang)
    {
        setlocale(LC_ALL, $lang, $lang . '.utf8', $lang . '.UTF8', $lang . 'utf-8', $lang . '.UTF-8');
        $this->_lang = $lang;
    }

    public function getAvailableLang($langCode)
    {
        $availableLangs      = $this->getAvailable();
        $availableShortLangs = [];
        foreach ($availableLangs as $availableLang) {
            $availableShortLangs[substr($availableLang, 0, 2)] = $availableLang;
        }
        if (in_array($langCode, $availableLangs)) {
            return $langCode;
        }
        if (array_key_exists($langCode, $availableShortLangs)) {
            return $availableShortLangs[$langCode];
        }

        return false;
    }

    public function setGlobalLang($lang)
    {
        $this->setLang($lang);
        Config::set("i18n.rootUrl", config("app.rootUrl") . $this->getShortLang() . '/');
    }

    protected function detect()
    {
        if (config("i18n.autodetect")) {
            $acceptedLangs = $this->getLanguagesFromHeader();
            foreach ($acceptedLangs as $acceptedLang => $priority) {
                if ($langMatch = $this->getAvailableLang($acceptedLang)) {
                    return $langMatch;
                }
            }
        }

        return $defaultLang = config("lang");
    }

    public function disableAutoTranslationOfModels()
    {
        $this->_autoTranslationOfModels = false;
    }

    public function enableAutoTranslationOfModels()
    {
        $this->_autoTranslationOfModels = true;
    }

    public function autoTranslateModels($enabled = null)
    {
        if (isset($enabled)) {
            $this->_autoTranslationOfModels = $enabled;
        }

        return $this->_autoTranslationOfModels;
    }

    protected function getLanguagesFromHeader()
    {
        // @source http://www.thefutureoftheweb.com/blog/use-accept-language-header
        $langs = [];
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            // break up string into pieces (languages and q factors)
            preg_match_all('/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
                $_SERVER['HTTP_ACCEPT_LANGUAGE'], $lang_parse);
            if (count($lang_parse[1])) {
                // create a list like "en" => 0.8
                $langs = array_combine($lang_parse[1], $lang_parse[4]);

                // set default to 1 for any without q factor
                foreach ($langs as $lang => $val) {
                    if ($val === '') {
                        $langs[$lang] = 1;
                    }
                }

                // sort list based on value
                arsort($langs, SORT_NUMERIC);
            }
        }

        return $langs;
    }

    public function getAvailable()
    {
        $langs  = Ajde_Fs_Find::findFiles(LANG_DIR, '*');
        $return = [];
        foreach ($langs as $lang) {
            $return[] = basename($lang);
        }

        return $return;
    }

    public function getAvailableNiceNames()
    {
        $langs  = Ajde_Fs_Find::findFiles(LANG_DIR, '*');
        $return = [];
        foreach ($langs as $lang) {
            $lang          = basename($lang);
            $return[$lang] = $this->getNiceName($lang);
        }

        return $return;
    }

    /**
     * @return Ajde_Lang_Adapter_Abstract
     */
    public function getAdapter()
    {
        if ($this->_adapter === null) {
            $adapterName    = 'Ajde_Lang_Adapter_' . ucfirst(config("i18n.adapter"));
            $this->_adapter = new $adapterName();
        }

        return $this->_adapter;
    }

    public function translate($ident, $module = null)
    {
        return $this->getAdapter()->get($ident, $module);
    }

    public function get($ident, $module = null)
    {
        // TODO:
        throw new Ajde_Core_Exception_Deprecated('Use Ajde_Lang::translate() instead');
    }

    public function getNiceName($lang = null)
    {
        if (!$lang) {
            $lang = $this->getLang();
        }

        return $this->niceName($lang);
    }

    public static function niceName($lang)
    {
        return self::$_niceNames[substr(strtolower($lang), 0, 2)];
    }

}
