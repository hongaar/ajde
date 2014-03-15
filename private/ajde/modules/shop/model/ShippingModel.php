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
	
	public function isEUCountry($country)
	{
		$eu_countries = array(
			"Albania",
			"Andorra",
			"Armenia",
			"Austria",
			"Azerbaijan",
			"Belarus",
			"Belgium",
			"Bosnia and Herzegovina",
			"Bulgaria",
			"Croatia",
			"Cyprus",
			"Czech Republic",
			"Denmark",
			"Estonia",
			"Finland",
			"France",
			"Georgia",
			"Germany",
			"Greece",
			"Hungary",
			"Iceland",
			"Ireland",
			"Italy",
			"Kosovo",
			"Latvia",
			"Liechtenstein",
			"Lithuania",
			"Luxembourg",
			"Macedonia",
			"Malta",
			"Moldova",
			"Monaco",
			"Montenegro",
			"The Netherlands",
			"Norway",
			"Poland",
			"Portugal",
			"Romania",
			"Russia",
			"San Marino",
			"Serbia",
			"Slovakia",
			"Slovenia",
			"Spain",
			"Sweden",
			"Switzerland",
			"Turkey",
			"Ukraine",
			"United Kingdom",
			"Vatican City",
			);
		return in_array($country, $eu_countries);
	}
}