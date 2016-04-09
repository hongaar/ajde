<?php

class SubmissionModel extends Ajde_Model
{
    protected $_autoloadParents = true;
    protected $_displayField    = 'id';

    protected $_jsonFields = ['entry'];

    public function displayAgo()
    {
        $timestamp = new DateTime($this->get('added'));
        $timestamp = $timestamp->format('U');

        return Ajde_Component_String::time2str($timestamp);
    }
}
