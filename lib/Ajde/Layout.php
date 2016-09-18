<?php

class Ajde_Layout extends Ajde_Template
{
    public function __construct($name, $style = 'default', $format = null)
    {
        $this->setName($name);
        $this->setStyle($style);

        $base = LAYOUT_DIR.$this->getName().DIRECTORY_SEPARATOR;
        $action = $this->getStyle();
        if (!$format) {
            if ((Ajde_Http_Request::isAjax() && $this->exist($base, $action, 'ajax'))
                || Ajde::app()->getDocument()->getFormat() === 'ajax'
            ) {
                $format = 'ajax';
            } else {
                if (Ajde::app()->getDocument()->getFormat() === 'crud') {
                    $format = 'crud';
                } else {
                    $format = 'html';
                }
            }
        }
        parent::__construct($base, $action, $format);
    }

    public function setName($name)
    {
        $this->set('name', $name);
    }

    public function setStyle($style)
    {
        $this->set('style', $style);
    }

    public function getName()
    {
        return $this->get('name');
    }

    public function getStyle()
    {
        return $this->get('style');
    }

    public function getFormat()
    {
        return $this->get('format');
    }

    public function getDocument()
    {
        return $this->get('document');
    }

    public function setDocument(Ajde_Document $document)
    {
        return $this->set('document', $document);
    }

    public function getDefaultResourcePosition()
    {
        return Ajde_Document_Format_Html::RESOURCE_POSITION_FIRST;
    }

    public function requireTimeoutWarning()
    {
        $this->requireJs('core.timeout', 'html', LAYOUT_DIR.'admin/');
    }
}
