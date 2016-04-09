<?php

class TntModel extends Ajde_Shop_Shipping_Method
{
    public function getDescription()
    {
        return 'Tnt';
    }

    public function getTotal()
    {
        $total = (float)$this->getTransaction()->shipment_itemstotal;
        if ($total > 500) {
            return 0;
        } else {
            if ($total > 100) {
                return ShippingModel::isEUCountry($this->getTransaction()->shipment_country) ? 15.95 : 22.50;
            } else {
                return ShippingModel::isEUCountry($this->getTransaction()->shipment_country) ? 7.50 : 12.50;
            }
        }
    }

}
