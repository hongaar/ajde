<?php

class ShippingModel extends Ajde_Shop_Shipping
{
    public function __construct(Ajde_Shop_Transaction $transaction)
    {
        parent::__construct($transaction);

        $country = $transaction->shipment_country;

        $this->addMethod(new TntModel());

        switch (strtolower($country)) {
            case 'the netherlands':
                $this->addMethod(new PickupModel());
        }
    }

    public static function isEUCountry($country)
    {
        $eu_countries = [
            "albania",
            "andorra",
            "armenia",
            "austria",
            "azerbaijan",
            "belarus",
            "belgium",
            "bosnia and herzegovina",
            "bulgaria",
            "croatia",
            "cyprus",
            "czech republic",
            "denmark",
            "estonia",
            "finland",
            "france",
            "georgia",
            "germany",
            "greece",
            "hungary",
            "iceland",
            "ireland",
            "italy",
            "kosovo",
            "latvia",
            "liechtenstein",
            "lithuania",
            "luxembourg",
            "macedonia",
            "malta",
            "moldova",
            "monaco",
            "montenegro",
            "the netherlands",
            "norway",
            "poland",
            "portugal",
            "romania",
            "russia",
            "san marino",
            "serbia",
            "slovakia",
            "slovenia",
            "spain",
            "sweden",
            "switzerland",
            "turkey",
            "ukraine",
            "united kingdom",
            "vatican city",
        ];

        return in_array(strtolower($country), $eu_countries);
    }
}
