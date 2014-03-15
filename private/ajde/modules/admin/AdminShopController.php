<?php 

class AdminShopController extends AdminController
{	
	public function carts()
	{
		Ajde::app()->getDocument()->setTitle("Carts overview");
		return $this->render();
	}

    public function transactions()
    {
        Ajde::app()->getDocument()->setTitle("Transactions overview");
        return $this->render();
    }
}