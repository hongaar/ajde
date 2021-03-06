<?php

class Ajde_Model extends Ajde_Object_Standard
{
    const ENCRYPTION_PREFIX = '$$$ENCRYPTED$$$';

    protected $_connection;
    protected $_table;

    protected $_autoloadParents = false;

    protected $_tableName;
    protected $_displayField = null;
    protected $_encrypedFields = [];
    protected $_jsonFields = [];

    protected $_hasMeta = false;
    protected $_metaLookup = [];
    public $_metaValues = [];

    protected $_validators = [];

    public static function extendController(Ajde_Controller $controller, $method, $arguments)
    {
        // Register getModel($name) function on Ajde_Controller
        if ($method === 'getModel') {
            if (!isset($arguments[0])) {
                $arguments[0] = $controller->getModule();
            }

            return self::getModel($arguments[0]);
        }
        // TODO: if last triggered in event cueue, throw exception
        // throw new Ajde_Exception("Call to undefined method ".get_class($controller)."::$method()", 90006);
        // Now, we give other callbacks in event cueue chance to return
    }

    /**
     * @param string $name
     *
     * @return Ajde_Model
     */
    public static function getModel($name)
    {
        $modelName = ucfirst($name).'Model';

        return new $modelName();
    }

    /**
     * @return Ajde_Collection
     */
    public function getCollection()
    {
        $collectionName = $this->toCamelCase($this->_tableName).'Collection';

        return new $collectionName();
    }

    public function __construct()
    {
        if (empty($this->_tableName)) {
            $tableNameCC = str_replace('Model', '', get_class($this));
            $this->_tableName = $this->fromCamelCase($tableNameCC);
        }

        $this->_connection = Ajde_Db::getInstance()->getConnection();
        $this->_table = Ajde_Db::getInstance()->getTable($this->_tableName);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    protected function _set($name, $value)
    {
        parent::_set($name, $value);
        if ($this->isFieldEncrypted($name)) {
            parent::_set($name, $this->encrypt($name));
        }
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    protected function _get($name)
    {
        if ($this->isFieldEncrypted($name)) {
            return $this->decrypt($name);
        }

        return parent::_get($name);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __toString()
    {
        if (empty($this->_data)) {
            return '';
        }

        return (string) $this->getPK();
    }

    public function __sleep()
    {
        return ['_autoloadParents', '_displayField', '_encrypedFields', '_data'];
    }

    public function __wakeup()
    {
        $this->__construct();
    }

    public function reset()
    {
        $this->_metaValues = [];
        parent::reset();
    }

    public function isEmpty($key)
    {
        $value = (string) $this->get($key);

        return empty($value);
    }

    public function getPK()
    {
        if (empty($this->_data)) {
            return;
        }
        $pk = $this->getTable()->getPK();

        return $this->has($pk) ? $this->get($pk) : null;
    }

    public function getDisplayField()
    {
        if (isset($this->_displayField)) {
            return $this->_displayField;
        } else {
            return current($this->getTable()->getFieldNames());
        }
    }

    public function displayField()
    {
        $displayField = $this->getDisplayField();

        return $this->has($displayField) ? $this->get($displayField) : '(untitled model)';
    }

    public function getPublishData()
    {
        return [
            'title'   => $this->displayField(),
            'message' => null,
            'image'   => null,
            'url'     => null,
        ];
    }

    public function getPublishRecipients()
    {
        return [];
    }

    /**
     * @return Ajde_Db_Adapter_Abstract
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @return Ajde_Db_Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    public function populate($array)
    {
        // TODO: parse array and typecast to match fields settings
        $this->_data = array_merge($this->_data, $array);
    }

    public function getValues($recursive = true)
    {
        $return = [];
        foreach ($this->_data as $k => $v) {
            if ($v instanceof self) {
                if ($recursive) {
                    $return[$k] = $v->getValues();
                } else {
                    @$return[$k] = (string) $v;
                }
            } else {
                $return[$k] = $v;
            }
        }

        return $return;
    }

    // Load model values
    public function loadByPK($value)
    {
        $this->reset();
        $pk = $this->getTable()->getPK();

        return $this->loadByFields([$pk => $value]);
    }

    public function loadByField($field, $value)
    {
        return $this->loadByFields([$field => $value]);
    }

    public function loadByFields($array)
    {
        $sqlWhere = [];
        $values = [];
        foreach ($array as $field => $value) {
            $sqlWhere[] = $field.' = ?';
            if ($this->isFieldEncrypted($field)) {
                $values[] = $this->doEncrypt($value);
            } else {
                $values[] = $value;
            }
        }
        $sql = 'SELECT * FROM '.$this->_table.' WHERE '.implode(' AND ', $sqlWhere).' LIMIT 1';

        return $this->_load($sql, $values);
    }

    protected function _load($sql, $values, $populate = true)
    {
        $statement = $this->getConnection()->prepare($sql);
        $statement->execute($values);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result === false || empty($result)) {
            return false;
        } else {
            if ($populate === true) {
                $this->reset();
                $this->loadFromValues($result);
            }
        }

        return true;
    }

    public function loadFromValues($values)
    {
        $this->populate($values);
        if ($this->_autoloadParents === true) {
            $this->loadParents();
        }
        if ($this->_hasMeta === true) {
            $this->populateMeta();
        }
    }

    private function getMetaTable()
    {
        return $this->getTable().'_meta';
    }

    public function populateMeta($values = false, $override = true)
    {
        if (!$values) {
            $values = $this->getMetaValues();
        }
        foreach ($values as $metaId => $value) {
            if ($override || !$this->has('meta_'.$metaId)) {
                $this->set('meta_'.$metaId, $value);
            }
        }
    }

    public function getMetaValues()
    {
        if (empty($this->_metaValues)) {
            $meta = [];
            if ($this->hasLoaded()) {
                $sql = 'SELECT * FROM '.$this->getMetaTable().' WHERE '.$this->getTable().' = ?';
                $statement = $this->getConnection()->prepare($sql);
                $statement->execute([$this->getPK()]);
                $results = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($results as $result) {
                    if (isset($meta[$result['meta']])) {
                        if (is_array($meta[$result['meta']])) {
                            $meta[$result['meta']][] = $result['value'];
                        } else {
                            $meta[$result['meta']] = [
                                $meta[$result['meta']],
                                $result['value'],
                            ];
                        }
                    } else {
                        $meta[$result['meta']] = $result['value'];
                    }
                }
            }
            $this->_metaValues = $meta;
        }

        return $this->_metaValues;
    }

    private function fuzzyMetaName($name)
    {
        return str_replace(' ', '_', strtolower($name));
    }

    public function lookupMetaName($name)
    {
        if (empty($this->_metaLookup)) {
            // We need to have the MetaModel here..
            $metaCollection = new MetaCollection();
            // disable join, as we don't get any metas which don't have a row yet
            //			$metaCollection->addFilter(new Ajde_Filter_Join($this->getMetaTable(), 'meta.id', 'meta'));
            foreach ($metaCollection as $meta) {
                /* @var $meta MetaModel */
                $this->_metaLookup[$this->fuzzyMetaName($meta->get('name'))] = $meta->getPK();
            }
        }
        if (isset($this->_metaLookup[$this->fuzzyMetaName($name)])) {
            return $this->_metaLookup[$this->fuzzyMetaName($name)];
        }

        return false;
    }

    public function getMetaValue($metaId)
    {
        if (!is_numeric($metaId)) {
            $metaId = $this->lookupMetaName($metaId);
        }
        $values = $this->getMetaValues();
        if (isset($values[$metaId])) {
            return $values[$metaId];
        }

        return false;
    }

    public function getMediaModelFromMetaValue($metaId)
    {
        $metaValue = (int) $this->getMetaValue($metaId);
        $media = new MediaModel();
        $media->loadByPK($metaValue);

        return $media->hasLoaded() ? $media : false;
    }

    public function getNodeModelFromMetaValue($metaId)
    {
        $metaValue = (int) $this->getMetaValue($metaId);
        $node = new NodeModel();
        $node->loadByPK($metaValue);

        return $node->hasLoaded() ? $node : false;
    }

    public function saveMeta()
    {
        foreach ($this->getValues() as $key => $value) {
            // don't ignore empty values
            //			if (substr($key, 0, 5) === 'meta_' && $value) {
            if (substr($key, 0, 5) === 'meta_') {
                $metaId = str_replace('meta_', '', $key);
                $this->saveMetaValue($metaId, $value);
            }
        }
    }

    public function saveMetaValue($metaId, $value)
    {
        if (!is_numeric($metaId)) {
            $metaId = $this->lookupMetaName($metaId);
        }

        $this->deleteMetaValue($metaId);

        // Get meta stuff
        $meta = new MetaModel();
        $meta->loadByPK($metaId);
        $metaType = Ajde_Crud_Cms_Meta::fromType($meta->getType());

        // Before save
        $value = $metaType->beforeSave($meta, $value, $this);

        // Insert new ones
        $sql = 'INSERT INTO '.$this->getMetaTable().' ('.$this->getTable().', meta, VALUE) VALUES (?, ?, ?)';
        $statement = $this->getConnection()->prepare($sql);
        $statement->execute([$this->getPK(), $metaId, $value]);

        // After save
        $metaType->afterSave($meta, $value, $this);

        $this->_metaValues[$metaId] = $value;
    }

    public function deleteMetaValue($metaId)
    {
        if (!is_numeric($metaId)) {
            $metaId = $this->lookupMetaName($metaId);
        }

        // Delete old records
        $sql = 'DELETE FROM '.$this->getMetaTable().' WHERE '.$this->getTable().' = ? AND meta = ?';
        $statement = $this->getConnection()->prepare($sql);
        $statement->execute([$this->getPK(), $metaId]);
    }

    /**
     * @param array $fields
     */
    public function setEncryptedFields($fields)
    {
        $this->_encrypedFields = $fields;
    }

    /**
     * @param string $field
     */
    public function addEncryptedField($field)
    {
        $this->_encrypedFields[] = $field;
    }

    /**
     * @return array
     */
    public function getEncryptedFields()
    {
        return $this->_encrypedFields;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    public function isFieldEncrypted($field)
    {
        return in_array($field, $this->getEncryptedFields());
    }

    /**
     * @return array
     */
    public function getJsonFields()
    {
        return $this->_jsonFields;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    public function isFieldJson($field)
    {
        return in_array($field, $this->getJsonFields());
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (method_exists($this, 'beforeSave')) {
            $this->beforeSave();
        }
        $pk = $this->getTable()->getPK();

        $sqlSet = [];
        $values = [];

        // encryption
        foreach ($this->getEncryptedFields() as $field) {
            if ($this->has($field)) {
                parent::_set($field, $this->encrypt($field));
            }
        }

        foreach ($this->getTable()->getFieldNames() as $field) {
            // Don't save a field is it's empty or not set
            if ($this->has($field)) {
                if ($this->getTable()->getFieldProperties($field,
                        'type') === Ajde_Db::FIELD_TYPE_DATE && parent::_get($field) instanceof DateTime
                ) {
                    $sqlSet[] = $field.' = ?';
                    $values[] = parent::_get($field)->format('Y-m-d h:i:s');
                } elseif ($this->getTable()->getFieldProperties($field,
                        'isAutoUpdate') && !$this->get($field) instanceof Ajde_Db_Function
                ) {
                    // just ignore this field
                } elseif ($this->isEmpty($field) && !$this->getTable()->getFieldProperties($field, 'isRequired')) {
                    $sqlSet[] = $field.' = NULL';
                } elseif (!$this->isEmpty($field)) {
                    if ($this->get($field) instanceof Ajde_Db_Function) {
                        $sqlSet[] = $field.' = '.(string) $this->get($field);
                    } elseif ($this->getTable()->getFieldProperties($field, 'type') === Ajde_Db::FIELD_TYPE_SPATIAL) {
                        $pointValues = explode(' ', (string) parent::_get($field));
                        $sqlSet[] = $field.' = PointFromWKB(POINT('.str_replace(',', '.',
                                (float) $pointValues[0]).','.str_replace(',', '.', (float) $pointValues[1]).'))';
                    } else {
                        $sqlSet[] = $field.' = ?';
                        $value = parent::_get($field);
                        if (is_float($value)) {
                            $values[] = $value;
                        } else {
                            $values[] = (string) $value;
                        }
                    }
                } elseif ($this->get($field) === 0 || $this->get($field) === '0') {
                    $sqlSet[] = $field.' = ?';
                    $values[] = (string) parent::_get($field);
                } else {
                    // Field is required but has an empty value..
                    // (shouldn't have passed validation)
                    // TODO: set to empty string or ignore?
                }
            }
        }
        $values[] = $this->getPK();
        $sql = 'UPDATE '.$this->_table.' SET '.implode(', ', $sqlSet).' WHERE '.$pk.' = ?';
        $statement = $this->getConnection()->prepare($sql);
        $return = $statement->execute($values);
        if ($this->_hasMeta === true) {
            $this->saveMeta();
        }
        if (method_exists($this, 'afterSave')) {
            $this->afterSave();
        }

        return $return;
    }

    public function insert($pkValue = null, $skipBeforeInsert = false)
    {
        if (method_exists($this, 'beforeInsert') && $skipBeforeInsert === false) {
            $this->beforeInsert();
        }
        $pk = $this->getTable()->getPK();
        if (isset($pkValue)) {
            $this->set($pk, $pkValue);
        } else {
            $this->set($pk, null);
        }
        $sqlFields = [];
        $sqlValues = [];
        $values = [];

        // encryption
        foreach ($this->getEncryptedFields() as $field) {
            if ($this->has($field)) {
                parent::_set($field, $this->encrypt($field));
            }
        }

        foreach ($this->getTable()->getFieldNames() as $field) {
            // Don't save a field is it's empty or not set
            if ($this->has($field) && $this->getTable()->getFieldProperties($field,
                    'type') === Ajde_Db::FIELD_TYPE_DATE && parent::_get($field) instanceof DateTime
            ) {
                $sqlFields[] = $field;
                $sqlValues[] = '?';
                $values[] = parent::_get($field)->format('Y-m-d h:i:s');
            } else {
                if ($this->has($field) && !$this->isEmpty($field)) {
                    if ($this->get($field) instanceof Ajde_Db_Function) {
                        $sqlFields[] = $field;
                        $sqlValues[] = (string) $this->get($field);
                    } elseif ($this->getTable()->getFieldProperties($field, 'type') === Ajde_Db::FIELD_TYPE_SPATIAL) {
                        $sqlFields[] = $field;
                        $pointValues = explode(' ', (string) parent::_get($field));
                        $sqlValues[] = 'PointFromWKB(POINT('.str_replace(',', '.',
                                (float) $pointValues[0]).','.str_replace(',', '.', (float) $pointValues[1]).'))';
                    } else {
                        $sqlFields[] = $field;
                        $sqlValues[] = '?';
                        $value = parent::_get($field);
                        if (is_float($value)) {
                            $values[] = $value;
                        } else {
                            $values[] = (string) $value;
                        }
                    }
                } else {
                    if ($this->has($field) && ($this->get($field) === 0 || $this->get($field) === '0')) {
                        $sqlFields[] = $field;
                        $sqlValues[] = '?';
                        $values[] = (string) parent::_get($field);
                    } else {
                        parent::_set($field, null);
                    }
                }
            }
        }
        $sql = 'INSERT INTO '.$this->_table.' ('.implode(', ', $sqlFields).') VALUES ('.implode(', ',
                $sqlValues).')';
        $statement = $this->getConnection()->prepare($sql);
        $return = $statement->execute($values);
        if (!isset($pkValue)) {
            $this->set($pk, $this->getConnection()->lastInsertId());
        }
        if ($this->_hasMeta === true) {
            $this->saveMeta();
        }
        if (method_exists($this, 'afterInsert')) {
            $this->afterInsert();
        }

        return $return;
    }

    public function delete()
    {
        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete();
        }
        $id = $this->getPK();
        $pk = $this->getTable()->getPK();
        $sql = 'DELETE FROM '.$this->_table.' WHERE '.$pk.' = ? LIMIT 1';
        $statement = $this->getConnection()->prepare($sql);
        try {
            $return = $statement->execute([$id]);
        } catch (Ajde_Db_IntegrityException $e) {
            return false;
        }
        if (method_exists($this, 'afterDelete')) {
            $this->afterDelete();
        }

        return $return;
    }

    public function hasParent($parent)
    {
        if (!$parent instanceof Ajde_Db_Table) {
            // throws error if no table can be found
            $parent = new Ajde_Db_Table($parent);
        }
        $fk = $this->getTable()->getFK($parent);

        return $fk;
    }

    public function getParents()
    {
        return $this->getTable()->getParents();
    }

    public function exists()
    {
        return (bool) $this->getPK();
    }

    public function hasLoaded()
    {
        return !empty($this->_data);
    }

    public function hasParentLoaded($parent)
    {
        return $this->has($parent) && $this->get($parent) instanceof self && $this->get($parent)->hasLoaded();
    }

    public function loadParents()
    {
        foreach ($this->getParents() as $column) {
            $this->loadParent($column);
        }
    }

    public function loadParent($column)
    {
        if (empty($this->_data)) {
            // TODO:
            throw new Ajde_Exception('Model '.(string) $this->getTable().' not loaded when loading parent');
        }
        if ($this->hasParentLoaded($column)) {
            return;
        }
        if (!$this->has($column)) {
            // No value for FK field
            return false;
        }
        $parentModel = $this->getParentModel($column);
        if ($parentModel === false) {
            // TODO:
            throw new Ajde_Exception('Could not load parent model \''.$column."'");
        }
        if ($parentModel->getTable()->getPK() != $this->getParentField($column)) {
            // TODO:
            throw new Ajde_Exception('Constraints on non primary key fields are currently not supported');
        }
        $parentModel->loadByPK($this->get($column));
        $this->set($column, $parentModel);
    }

    /**
     * @param string $column
     *
     * @return Ajde_Model
     */
    public function getParentModel($column)
    {
        $parentModelName = ucfirst($this->getParentTable($column)).'Model';
        if (!class_exists($parentModelName)) {
            return false;
        }

        return new $parentModelName();
    }

    public function getParentTable($column)
    {
        $fk = $this->getTable()->getFK($column);

        return strtolower($fk['parent_table']);
    }

    public function getParentField($column)
    {
        $fk = $this->getTable()->getFK($column);

        return strtolower($fk['parent_field']);
    }

    public function getAutoloadParents()
    {
        return $this->_autoloadParents;
    }

    public function addValidator($fieldName, Ajde_Model_ValidatorAbstract $validator)
    {
        $validator->setModel($this);
        if (!isset($this->_validators[$fieldName])) {
            $this->_validators[$fieldName] = [];
        }
        $this->_validators[$fieldName][] = $validator;
    }

    public function getValidators()
    {
        return $this->_validators;
    }

    public function getValidatorsFor($fieldName)
    {
        if (!isset($this->_validators[$fieldName])) {
            $this->_validators[$fieldName] = [];
        }

        return $this->_validators[$fieldName];
    }

    public function validate($fieldOptions = [])
    {
        if (method_exists($this, 'beforeValidate')) {
            $return = $this->beforeValidate();
            if ($return !== true && $return !== false) {
                // TODO:
                throw new Ajde_Exception(sprintf('beforeValidate() must return either TRUE or FALSE'));
            }
            if ($return === false) {
                return false;
            }
        }

        if (!$this->hasLoaded()) {
            return false;
        }
        $errors = [];
        $validator = $this->_getValidator();

        $valid = $validator->validate($fieldOptions);
        if (!$valid) {
            $errors = $validator->getErrors();
        }
        $this->setValidationErrors($errors);

        if (method_exists($this, 'afterValidate')) {
            $this->afterValidate();
        }

        return $valid;
    }

    public function getValidationErrors()
    {
        return parent::getValidationErrors();
    }

    /**
     * @return Ajde_Model_Validator
     */
    private function _getValidator()
    {
        return new Ajde_Model_Validator($this);
    }

    public function hash()
    {
        $str = implode('', $this->valuesAsSingleDimensionArray());

        return md5($str);
    }

    public function isEncrypted($field)
    {
        return substr_count(Ajde_Component_String::decrypt(parent::_get($field)),
            self::ENCRYPTION_PREFIX.config('security.secret'));
    }

    public function encrypt($field)
    {
        if (!$this->isEmpty($field)) {
            if ($this->isEncrypted($field)) {
                return parent::_get($field);
            }

            return $this->doEncrypt(parent::_get($field));
        }
    }

    public function doEncrypt($string)
    {
        return Ajde_Component_String::encrypt(self::ENCRYPTION_PREFIX.config('security.secret').$string);
    }

    public function decrypt($field)
    {
        if (!$this->isEncrypted($field)) {
            return parent::_get($field);
        }
        $decrypted = str_replace(self::ENCRYPTION_PREFIX.config('security.secret'), '',
            Ajde_Component_String::decrypt(parent::_get($field)));

        return $decrypted;
    }

    // TREE SORT FUNCTIONS

    public function sortTree($collectionName, $parentField = 'parent', $levelField = 'level', $sortField = 'sort')
    {
        $collection = new $collectionName();
        $collection->addFilter(new Ajde_Filter_Where($parentField, Ajde_Filter::FILTER_IS, null));
        $collection->orderBy($sortField);

        // Start at root path
        $this->_recurseChildren($collection, $collectionName, $parentField, $levelField, $sortField);
    }

    private function _recurseChildren(
        $collection,
        $collectionName,
        $parentField,
        $levelField,
        $sortField,
        $updatedField = 'updated'
    ) {
        static $sort;
        static $level;
        /** @var $item Ajde_Acl_Proxy_Model */
        foreach ($collection as $item) {
            $sort++;
            $item->set($sortField, $sort);
            $item->set($levelField, $level);
            $item->set($updatedField, new Ajde_Db_Function($updatedField));
            if ($item instanceof Ajde_Acl_Proxy_Model) {
                if ($item->validateAccess('update', false)) {
                    $item->save();
                }
            } else {
                $item->save();
            }
            // Look for children
            $children = new $collectionName();
            $children->addFilter(new Ajde_Filter_Where($parentField, Ajde_Filter::FILTER_EQUALS, $item->getPK()));
            $children->orderBy($sortField);
            if ($children->count()) {
                $level++;
                $this->_recurseChildren($children, $collectionName, $parentField, $levelField, $sortField);
            }
        }
        $level--;
    }
}
