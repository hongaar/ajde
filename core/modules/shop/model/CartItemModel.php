<?php

class CartItemModel extends Ajde_Shop_Cart_Item
{
    protected $_cartModel = 'CartModel';

    public function getDescription()
    {
        $entity = $this->getEntity();
        if (!$entity instanceof Ajde_Model || !$entity->hasLoaded()) {
            return __('(item is unavailable)');
        }

        return $entity->getTitle();
    }
}
