<?php

class Ajde_Shop_Transaction_Provider_Mollie_Ideal extends Ajde_Shop_Transaction_Provider_Mollie
{
    public function getName()
    {
        return 'iDeal';
    }

    public function getLogo()
    {
        return MEDIA_DIR . '_core/shop/ideal.png';
    }

    protected function getMethod()
    {
        return 'ideal';
    }
}
