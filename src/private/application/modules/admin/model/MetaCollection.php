<?php

class MetaCollection extends Ajde_Collection
{
	public function concatCrossReference($crossReferenceTable, $crossReferenceField)
	{
		$this->addFilter(new Ajde_Filter_LeftJoin($crossReferenceTable, 'id', 'meta'));
		$this->getQuery()->addSelect(new Ajde_Db_Function(
			'GROUP_CONCAT(' . $crossReferenceTable . '.' . $crossReferenceField . ' ' .
			'ORDER BY ' . $crossReferenceTable . '.sort) AS ' . $crossReferenceField
		));
		$this->getQuery()->addGroupBy('meta.id');
		$this->orderBy($crossReferenceTable . '.sort');
		return $this;
	}
}
