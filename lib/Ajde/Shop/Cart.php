<?php

abstract class Ajde_Shop_Cart extends Ajde_Model
{
    protected $_autoloadParents = false;

    protected $_cartItemModel = null;
    protected $_cartItemCollection = null;

    private $_items;

    public function beforeInsert()
    {
        // Added
        $this->added = new Ajde_Db_Function("NOW()");
    }

    /**
     *
     * @return UserModel
     */
    public function loadCurrent()
    {
        $loaded = false;

        if ($user = UserModel::getLoggedIn()) {
            // Do we have a saved cart for logged in user?
            if ($this->loadByUser($user) === false) {
                $this->user = $user->getPK();
                $this->insert();
            }
        } else {
            // Do we have a cart from IP address?
            if ($this->loadByClient() === false) {
                $this->client = md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
                $this->insert();
            }
        }

        return true;
    }

    public function loadByUser(UserModel $user)
    {
        return $this->loadByField('user', $user->getPK());
    }

    public function loadByClient()
    {
        return $this->loadByField('client', md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']));
    }

    public function addItem($entity, $id = null, $qty = 1)
    {
        if (!$this->hasLoaded()) {
            // TODO:
            throw new Ajde_Exception("No shopping cart is loaded");
        }

        if ($entity instanceof Ajde_Model) {
            if (!$entity->hasLoaded()) {
                // TODO:
                throw new Ajde_Exception('Entity is instance of Ajde_Model but not loaded when calling Ajde_Shop_Cart::addItem()');
            }
            /* @var $entity Ajde_Model */
            $id = $entity->getPK();
            $entity = (string)$entity->getTable();
        } else {
            if (!isset($id)) {
                // TODO:
                throw new Ajde_Exception('No entity and entity_id given when calling Ajde_Shop_Cart::addItem()');
            }
        }
        /* @var $entity string */

        $cartItem = $this->getItem($entity, $id);
        /* @var $cartItem Ajde_Shop_Cart_Item */

        $this->updated = new Ajde_Db_Function("NOW()");

        if ($cartItem->hasLoaded()) {
            $cartItem->addQty($qty);
            $cartItem->save();
        } else {
            $cartItem->setEntityById($entity, $id);
            $cartItem->setQty($qty);
            $cartItem->insert();
        }

        $this->_items = null;
    }

    /**
     *
     * @param string $entity
     * @param integer $id
     * @return Ajde_Shop_Cart_Item
     */
    public function getItem($entity, $id)
    {
        $cartItem = $this->_getItemModel();
        $cartItem->load($entity, $id);

        return $cartItem;
    }

    /**
     *
     * @return Ajde_Shop_Cart_Item_Collection
     */
    public function getItems()
    {
        if (!isset($this->_items)) {
            $this->_items = $this->_getItemCollection();
        }

        return $this->_items;
    }

    public function hasItems()
    {
        return $this->getItems()->length();
    }

    public function emptyItems()
    {
        $cartItems = $this->getItems();

        $success = true;
        foreach ($cartItems as $item) {
            /* @var $item Ajde_Shop_Cart_Item */
            $success = $success * $item->delete();
        }

        $this->updated = new Ajde_Db_Function("NOW()");
        $this->save();

        return (bool)$success;
    }

    public function countItems()
    {
        return $this->getItems()->count();
    }

    public function countQty()
    {
        return $this->getItems()->countQty();
    }

    public function getHtmlSummaryTable()
    {
        $items = $this->getItems();
        $table = '<table class="table table-condensed"><thead>';
        $table .= '<tr>';
        $table .= '<th>' . __('Quantity') . '</th>';
        $table .= '<th>' . __('Description') . '</th>';
        $table .= '<th>' . __('VAT') . '</th>';
        $table .= '<th>' . __('Total') . '</th>';
        $table .= '</tr></thead><tbody>';
        foreach ($items as $item) {
            /* @var $item Ajde_Shop_Cart_Item */
            $table .= '<tr>';
            $table .= '<td>' . $item->getQty() . '</td>';
            $table .= '<td>' . $item->getDescription() . '</td>';
            $table .= '<td>' . $item->getFormattedVATAmount() . '</td>';
            $table .= '<td>' . $item->getFormattedTotal() . '</td>';
            $table .= '</tr>';
        }
        $table .= '</tbody><tfoot><tr>';
        $table .= '<td>' . $this->countQty() . '</td>';
        $table .= '<td>' . __('Total') . '</td>';
        $table .= '<td>' . $items->getFormattedVATAmount() . '</td>';
        $table .= '<td>' . $items->getFormattedTotal() . '</td>';
        $table .= '</tr></tfoot>';
        $table .= '</table>';

        return $table;
    }

    /**
     *
     * @return Ajde_Shop_Cart_Item
     */
    protected function _getItemModel()
    {
        $cartItemModelName = $this->_cartItemModel;

        return new $cartItemModelName($this);
    }

    protected function _getItemCollection()
    {
        $cartItemCollectionName = $this->_cartItemCollection;

        return new $cartItemCollectionName($this);
    }
}