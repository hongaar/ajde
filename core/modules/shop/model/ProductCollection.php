<?php

class ProductCollection extends Ajde_Collection
{
    public function filterPublished()
    {
        $this->addFilter(new Ajde_Filter_Where('published', Ajde_Filter::FILTER_EQUALS, 1));
    }

    public function load()
    {
        if (Ajde::app()->getRequest()->getParam('filterPublished', false) ==  true) {
            $this->filterPublished();
        }
        return parent::load();
    }
}
