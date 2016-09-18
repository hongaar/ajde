<?php

class Ajde_Component_Crud extends Ajde_Component
{
    public static function processStatic(Ajde_Template_Parser $parser, $attributes)
    {
        $instance = new self($parser, $attributes);

        return $instance->process();
    }

    protected function _init()
    {
        return [
            'list'       => 'list',
            'edit'       => 'edit',
            'mainfilter' => 'mainfilter',
        ];
    }

    public function process()
    {
        switch ($this->_attributeParse()) {
            case 'list':
                $options = issetor($this->attributes['options'], []);
                $crud = new Ajde_Crud($this->attributes['model'], $options);
                $crud->setAction('list');

                return $crud;
                break;
            case 'edit':
                $options = issetor($this->attributes['options'], []);
                $id = issetor($this->attributes['id'], null);
                $crud = new Ajde_Crud($this->attributes['model'], $options);
                $crud->setId($id);
                $crud->setAction('edit/layout');

                return $crud;
                break;
            case 'mainfilter':
                $controller = Ajde_Controller::fromRoute(new Ajde_Core_Route('_core/crud:mainfilter'));
                $controller->setCrudInstance($this->attributes['crud']);
                $controller->setRefresh(issetor($this->attributes['refresh'], false));

                return $controller->invoke();
        }
        // TODO:
        throw new Ajde_Component_Exception();
    }
}
