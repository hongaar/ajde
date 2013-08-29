<?php

class TntModel extends Ajde_Shop_Shipping_Method
{
	public function getDescription() {
		return 'Tnt';
	}
	
	public function getTotal() {
		$total = (float) $this->getTransaction()->shipment_itemstotal;
		if ($total > 500) {
			return 0;
		} else if ($total > 100) {
			return 15.95;
		} else {
			return 7.50;
		}
	}

}