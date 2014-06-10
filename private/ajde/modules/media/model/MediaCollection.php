<?php

class MediaCollection extends Ajde_Collection
{
    public function filterByType($type, $operator = Ajde_Query::OP_AND)
    {
        if (is_numeric($type)) {
            $this->addFilter(new Ajde_Filter_Where('mediatype', Ajde_Filter::FILTER_EQUALS, $type, $operator));
        } else {
            $niceName = str_replace('_', ' ', $type);
            $mediatype = new MediatypeModel();
            if ($mediatype->loadByField('name', $niceName)) {
                $this->addFilter(new Ajde_Filter_Where('mediatype', Ajde_Filter::FILTER_EQUALS, $mediatype->getPK(), $operator));
            }
        }
        return $this;
    }
}
