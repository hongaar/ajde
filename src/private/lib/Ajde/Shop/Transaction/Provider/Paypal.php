<?php

class Ajde_Shop_Transaction_Provider_Paypal extends Ajde_Shop_Transaction_Provider
{
    public function getName() {
		return 'PayPal';
	}
	
	public function getLogo() {
		return 'public/images/_core/shop/paypal.png';
	}
	
	public function usePostProxy() {
		return true;
	}
	
	public function getRedirectUrl() {
		$url = $this->isSandbox() ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
		return $this->ping($url) ? $url : false;
	}
	
	public function getRedirectParams() {
		$transaction = $this->getTransaction();
		return array(
			'cmd'			=> '_xclick',
			'business'		=> Config::get('shopPaypalAccount'),
			'notify_url'	=> 'http://' . Config::get('site_root') . 'shop/transaction:callback/paypal.html',
			'bn'			=> Config::get('ident') . '_BuyNow_WPS_' . strtoupper(Ajde_Lang::getInstance()->getShortLang()),
			'amount'		=> $transaction->payment_amount,
			'item_name'		=> Config::get('ident') . ': ' . Ajde_Component_String::makePlural($transaction->shipment_itemsqty, 'item'),
			'quantity'		=> 1,
			'address_ override' => 1,
			'address1'		=> $transaction->shipment_address,
			'zip'			=> $transaction->shipment_zipcode,
			'city'			=> $transaction->shipment_city,
			'state'			=> $transaction->shipment_region,
			'country'		=> $transaction->shipment_country,
			'email'			=> $transaction->email,
			'first_name'	=> $transaction->name,
			'currency_code'	=> Config::get('currencyCode'),
			'custom'		=> $transaction->secret,
			'no_shipping'	=> 1, // do not prompt for an address
			'no_note'		=> 1, // hide the text box and the prompt
			'return'		=> 'http://' . Config::get('site_root') . 'shop/transaction:complete',
			'rm'			=> 1 // the buyer’s browser is redirected to the return URL by using the GET method, but no payment variables are included
		);
	}
	
	public function updatePayment()
	{
		// PHP 4.1

		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=_notify-validate';

		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		// post back to PayPal system to validate
		$header = '';
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ($this->isSandbox() ? 'ssl://www.sandbox.paypal.com' : 'ssl://www.paypal.com', 443, $errno, $errstr, 30);

		// assign posted variables to local variables
		$item_name = $_POST['item_name'];
		$item_number = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$payment_amount = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id = $_POST['txn_id'];
		$receiver_email = $_POST['receiver_email'];
		$payer_email = $_POST['payer_email'];

		Ajde_Model::register('shop');
		$secret = $_POST['custom'];
		$transaction = new TransactionModel();
		if (!$transaction->loadByField('secret', $secret)) {			
			Ajde_Log::log('Could not find transaction for PayPal payment with txn id ' . $txn_id . ' and transaction secret ' . $secret);
		}
		
		if (!$fp) {
			// HTTP ERROR
		} else {
			fputs ($fp, $header . $req);
			while (!feof($fp)) {
				$res = fgets ($fp, 1024);
				if (strcmp ($res, "VERIFIED") == 0) {
					// check the payment_status is Completed
					if ($payment_status == 'Completed') {					
						$details =	'AMOUNT: '			. $payment_amount		. PHP_EOL .
									'CURRENCY: '		. $payment_currency		. PHP_EOL .
									'PAYER_EMAIL: '		. $payer_email			. PHP_EOL .
									'RECEIVER_EMAIL: '	. $receiver_email		. PHP_EOL .
									'TXN_ID: '			. $txn_id				. PHP_EOL;										
						$transaction->payment_details = $details;
						$transaction->payment_status = 'completed';
						$transaction->save();						
					} else {
						$transaction->payment_status = 'refused';
						$transaction->save();		
						Ajde_Log::log('Status is not Completed but ' . $payment_status . ' for PayPal payment with txn id ' . $txn_id . ' and transaction secret ' . $secret);
					}
					// check that txn_id has not been previously processed
					// check that receiver_email is your Primary PayPal email
					// check that payment_amount/payment_currency are correct
					// process payment
				} else if (strcmp ($res, "INVALID") == 0) {
					// log for manual investigation
					$transaction->payment_status = 'refused';
					$transaction->save();
					Ajde_Log::log('Validation failed for PayPal payment with txn id ' . $txn_id);
				}
			}
			fclose ($fp);
		}
	}
}