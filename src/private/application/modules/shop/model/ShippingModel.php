<?php

class ShippingModel extends Ajde_Shop_Shipping
{
	public function __construct(Ajde_Shop_Transaction $transaction) {
		parent::__construct($transaction);
		
		$country = $transaction->shipment_country;
		
		$this->addMethod(new TntModel());
		
		switch (strtolower($country)) {
			case 'netherlands':
				$this->addMethod(new PickupModel());				
		}
	}
}