<?php

abstract class Ajde_Filter extends Ajde_Object_Standard
{
    const FILTER_IS = ' IS ';
    const FILTER_NOT = ' IS NOT ';
    const FILTER_EQUALS = ' = ';
    const FILTER_EQUALSNOT = ' != ';
    const FILTER_GREATER = ' > ';
    const FILTER_GREATEROREQUAL = ' >= ';
    const FILTER_LESS = ' < ';
    const FILTER_LESSOREQUAL = ' <= ';
    const FILTER_LIKE = ' LIKE ';
    const FILTER_NOTLIKE = ' NOT LIKE ';
    const FILTER_IN = ' IN ';

    const CONDITION_WHERE = 'where';
    const CONDITION_HAVING = 'having';

    abstract public function prepare(Ajde_Db_Table $table = null);
}
