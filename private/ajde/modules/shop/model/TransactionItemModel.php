<?php

class TransactionItemModel extends Ajde_Model
{
    /**
     *
     * @return Ajde_Model
     */
    public function getEntity()
    {
        $entity = $this->get('entity_id');
        if (!$entity instanceof Ajde_Model)
        {
            $id = $entity;
            $entity = $this->_getEntityModel($this->get('entity'));
            $entity->loadByPK($id);
        }
        return $entity;
    }

    /**
     *
     * @param string $entityName
     * @return Ajde_Model
     */
    protected function _getEntityModel($entityName)
    {
        Ajde_Model::registerAll();
        $entityModelName = ucfirst((string) $entityName) . 'Model';
        return new $entityModelName();
    }
}