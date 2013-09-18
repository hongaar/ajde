<?php

class Ajde_Crud_Field_I18n extends Ajde_Crud_Field_Enum
{
	protected $_useSpan = false;
	
	public function __construct(Ajde_Crud $crud, $fieldOptions) {
		parent::__construct($crud, $fieldOptions);
		$this->set('default', Config::get('lang'));
	}
	
	public function getFieldsToClone()
	{
		return ($this->has('cloneFields') ? (array) $this->get('cloneFields') : array());
	}
	
	public function getValues()
	{
		$lang = Ajde_Lang::getInstance();
		$langs = $lang->getAvailableNiceNames();
		return $langs;
	}
		
	public function getAvailableTranslations()
	{
		$lang = Ajde_Lang::getInstance();
		$langs = $lang->getAvailableNiceNames();
		
		$model = $this->_crud->getModel();
		/* @var $model Ajde_Model_I18n */
		$translations = $model->getTranslations();
		
		$translatedLangs = array();
		foreach ($translations as $model) {
			/* @var $model Ajde_Model_I18n */
			$modelLanguage = $model->getLanguage();
			if (!empty($modelLanguage)) {
				$translatedLangs[$modelLanguage] = $model;
			}
		}
		
		foreach($langs as $key => &$name) {
			$name = array(
					'name' => $name,
				);
			
			if (array_key_exists($key, $translatedLangs)) {
				$name['model'] = $translatedLangs[$key];
			}
		}
		
		return $langs;
	}
	
	public function _getHtmlAttributes() {
		$attributes = array();
		$attributes['class'] = 'lang';
		return $attributes;
	}
}