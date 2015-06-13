<?php 

class AdminShopController extends AdminController
{
    public function menu()
    {
        return $this->render();
    }

    public function products()
    {
        Ajde::app()->getDocument()->setTitle("Product catalogue");
        return $this->render();
    }

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