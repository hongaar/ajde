<?php

class PickupModel extends Ajde_Shop_Shipping_Method
{
	public function getDescription() {
		return 'Pickup';
	}
	
	public function getTotal() {
		return 0;
	}

}