<?php

class MetaCollection extends Ajde_Collection
{
    public function concatCrossReference($crossReferenceTable, $crossReferenceField)
    {
        $this->addFilter(new Ajde_Filter_LeftJoin($crossReferenceTable, 'meta.id', $crossReferenceTable.'.meta'));
        $this->concatField($crossReferenceTable, $crossReferenceField);
        $this->getQuery()->addGroupBy('meta.id');
        $this->orderBy($crossReferenceTable.'.sort');

        return $this;
    }

    public function concatField($crossReferenceTable, $crossReferenceField)
    {
        $this->getQuery()->addSelect(new Ajde_Db_Function(
            'GROUP_CONCAT('.$crossReferenceTable.'.'.$crossReferenceField.' '.
            'ORDER BY '.$crossReferenceTable.'.sort) AS '.$crossReferenceField
        ));
    }
}
