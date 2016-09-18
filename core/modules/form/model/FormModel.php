<?php

class FormModel extends Ajde_Model
{
    protected $_autoloadParents = true;
    protected $_displayField = 'name';

    /**
     * @return EmailModel
     */
    public function getEmail()
    {
        $this->loadParent('email');

        return parent::get('email');
    }

    /**
     * @return MetaModel
     */
    public function getEmailTo()
    {
        $this->loadParent('email_to');

        return parent::get('email_to');
    }
}
