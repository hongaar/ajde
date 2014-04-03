<?php

class EmailModel extends Ajde_Model
{
	protected $_autoloadParents = true;
	protected $_displayField = 'name';

    public function beforeInsert()
    {
        $this->makeIdentifier();
    }

    public function beforeSave()
    {
        $this->makeIdentifier();
    }

    private function makeIdentifier()
    {
        $this->identifier = strtolower(str_replace(' ', '_', $this->name));
    }

    /**
     * @param bool|string $useLang
     * @return TemplateModel
     */
    public function getTemplate($useLang = false)
    {
        /* @var $template TemplateModel */
        $template = parent::getTemplate();
        $translation = $template->getTranslatedLazy($useLang);
        return $translation;
    }

    public function getFromName()
    {
        return parent::get('from_name');
    }

    public function getFromEmail()
    {
        return parent::get('from_email');
    }
}
