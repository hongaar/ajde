<?php

class Ajde_Db_Table extends Ajde_Object_Standard
{
    protected $_connection;
    protected $_name;
    protected $_fields;

    public function __construct($name)
    {
        $this->_name       = $name;
        $this->_connection = Ajde_Db::getInstance()->getConnection();
        $this->initTableStructure();
    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    public function initTableStructure()
    {
        $structure = Ajde_Db::getInstance()->getAdapter()->getTableStructure($this->_name);
        foreach ($structure as $field) {
            $fieldName       = $field['Field'];
            $fieldType       = $field['Type'];
            $fieldParsedType = Ajde_Db::getInstance()->getAdapter()->getFieldType($fieldType);
            $fieldDefault    = $field['Default'];
            $fieldLabel      = !empty($field['Comment']) ? $field['Comment'] : $field['Field'];

            $fieldIsRequired      = strtoupper($field['Null']) === 'NO';
            $fieldIsPK            = strtoupper($field['Key']) === 'PRI';
            $fieldIsAutoIncrement = strtolower($field['Extra']) === 'auto_increment';
            $fieldIsAutoUpdate    = strtolower($field['Extra']) === 'on update current_timestamp';
            $fieldIsUnique        = strtoupper($field['Key']) === 'UNI';

            // Fix for certain MySQL versions
            if (strtolower($fieldDefault) === 'current_timestamp') {
                $fieldIsAutoUpdate = true;
            }

            $this->_fields[$fieldName] = [
                'name'            => $fieldName,
                'dbtype'          => $fieldType,
                'type'            => $fieldParsedType['type'],
                'length'          => $fieldParsedType['length'],
                'default'         => $fieldDefault,
                'label'           => $fieldLabel,
                'isRequired'      => $fieldIsRequired,
                'isPK'            => $fieldIsPK,
                'isAutoIncrement' => $fieldIsAutoIncrement,
                'isAutoUpdate'    => $fieldIsAutoUpdate,
                'isUnique'        => $fieldIsUnique
            ];
        }
    }

    public function getPK()
    {
        foreach ($this->_fields as $field) {
            if ($field['isPK'] === true) {
                return $field['name'];
            }
        }

        return false;
    }

    /**
     *
     * @param Ajde_Db_Table|string $parent
     * @return array
     */
    public function getFK($column)
    {
        $fk = Ajde_Db::getInstance()->getAdapter()->getForeignKey((string)$this, (string)$column);

        return [
            'field'        => $fk['COLUMN_NAME'],
            'parent_table' => $fk['REFERENCED_TABLE_NAME'],
            'parent_field' => $fk['REFERENCED_COLUMN_NAME']
        ];
    }

    public function getParents()
    {
        $parents       = Ajde_Db::getInstance()->getAdapter()->getParents((string)$this);
        $parentColumns = [];
        foreach ($parents as $parent) {
            if (
                isset($parent['COLUMN_NAME'])
                && isset($parent['REFERENCED_TABLE_NAME'])
                && strtoupper($parent['CONSTRAINT_NAME']) != 'PRIMARY'
            ) {
                $parentColumns[] = $parent['COLUMN_NAME'];
            }
        }

        return array_unique($parentColumns);
    }

    public function getFieldProperties($fieldName = null, $property = null)
    {
        if (isset($fieldName)) {
            if (isset($this->_fields[$fieldName])) {
                if (isset($property)) {
                    if (isset($this->_fields[$fieldName][$property])) {
                        return $this->_fields[$fieldName][$property];
                    }
                } else {
                    return $this->_fields[$fieldName];
                }
            }
        } else {
            return $this->_fields;
        }
    }

    public function getFieldNames()
    {
        return array_keys($this->_fields);
    }

    public function getFieldLabels()
    {
        $labels = [];
        foreach ($this->_fields as $field) {
            $labels[$field['name']] = $field['label'];
        }

        return $labels;
    }

    public function __toString()
    {
        return $this->_name;
    }
}
