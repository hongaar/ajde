<?php

class Ajde_Shop_Transaction_Provider_Wedeal extends Ajde_Shop_Transaction_Provider
{
	private static $_debug		= false;
	private static $_api_url	= "www.paydutch.nl";
	private static $_api_path	= "/api/processreq.aspx";
	
	public function getName() {
		return 'iDeal';
	}
	
	public function getLogo() {
		return 'public/images/_core/shop/ideal.png';
	}
	
	public function usePostProxy() {
		return false;
	}
	
	public function getRedirectUrl() {
		$transaction = $this->getTransaction();		
		
		$total = (string) $total;
		$total = str_replace(".", ",", $total);
		
		$request = array(			
			"type" => "transaction",
			"transactionreq" => array(
				"username"		=> Config::get('shopWedealUsername'),
				"password"		=> Config::get('shopWedealPassword'),
				"reference"		=> $transaction->secret,
				"description"	=> Config::get('ident') . ': ' . Ajde_Component_String::makePlural($transaction->shipment_itemsqty, 'item'),
				"amount"		=> str_replace(".", ",", (string) $transaction->payment_amount),
				"methodcode"	=> "0101",
				"maxcount"		=> "1",
				"test"			=> $this->isSandbox() ? "true" : "false",
				"successurl"	=> 'http://' . Config::get('site_root') . 'shop/transaction:callback/wedeal.html',
				"failurl"		=> 'http://' . Config::get('site_root') . 'shop/transaction:callback/wedeal.html'
			)
		);
		$res = $this->sendRequest($request, true);
		
		if ($res['success'] === true) {
			$url = $res['response'];
			if (substr($url, 0, 7) === 'http://' || substr($url, 0, 8) === 'https://') {
				return $this->ping($url) ? $res['response'] : false;
			} else {
				Ajde_Log::log("Wedeal::getRedirectUrl() returned no URL but: " . $res['response']);
				return false;
			}
		} else {
			Ajde_Log::log("Wedeal::getRedirectUrl() returned no URL but: " . $res['response']);
			return false;
		}
	}
	
	public function getRedirectParams() {
		return array();
	}
	
	public function updatePayment() {
		$request = Ajde::app()->getRequest();
		
		$username		= $request->getParam('Username');
		$password		= $request->getParam('Password');
		$id				= $request->getParam('ID');
		$secret			= $request->getParam('Reference');
		$paymentMethod	= $request->getParam('PaymentMethod');
		$state			= $request->getParam('PaymentState');
		$description	= $request->getParam('Description');

		if ($username != Config::get('shopWedealCallbackUsername')) { Ajde_Log::log('Invalid username for callback of transaction ' . $secret); return false; }
		if ($password != Config::get('shopWedealCallbackPassword')) { Ajde_Log::log('Invalid password for callback of transaction ' . $secret); return false; }
		
		Ajde_Model::register('shop');
		$transaction = new TransactionModel();
		if (!$transaction->loadByField('secret', $secret)) {			
			Ajde_Log::log('Could not find transaction for PayPal payment with txn id ' . $txn_id . ' and transaction secret ' . $secret);
		}
		
		$request = array(
			"type" => 'query',
			"merchant" => array(
				"username"		=> Config::get('shopWedealUsername'),
				"password"		=> Config::get('shopWedealPassword'),
				"reference"		=> $secret,
			)			
		);
		$res = $this->sendRequest($request);
		
		if ($res['success'] === true) {			
			$response = $res['response']->paymentinfo;
			
			// get transaction details
			if ((int) $response->count == 0) {
				$transaction->payment_status = 'refused';
				$transaction->save();
				Ajde_Log::log('iDeal callback didn\'t return any transaction for ' . $secret);
				return false;
			} elseif (self::isPaid((string) $response->state)) {				
				if ((string) $response->id != $id) {
					Ajde_Log::log('IDs don\'t match for iDeal callback of transaction ' . $secret);
					return false;
				}
				$details =	'AMOUNT: '			. (string) $response->amount			. PHP_EOL .
							'PAYER_NAME: '		. (string) $response->consumername		. PHP_EOL .
							'PAYER_ACCOUNT: '	. (string) $response->consumeraccount	. PHP_EOL .
							'PAYER_CITY: '		. (string) $response->consumercity		. PHP_EOL .
							'PAYER_COUNTRY: '	. (string) $response->consumercountry	. PHP_EOL .
							'WEDEAL_ID: '		. (string) $response->id;
				$transaction->payment_details = $details;
				$transaction->payment_status = 'completed';
				$transaction->save();
				return true;
			} elseif (self::isRefused((string) $response->state)) {
				$transaction->payment_status = 'refused';
				$transaction->save();
				Ajde_Log::log("iDeal payment refused with state " . (string) $response->state);
				return false;
			}
			Ajde_Log::log("iDeal payment callback called with state " . (string) $response->state . " but no status change for transaction " . $secret . " detected");
			return false;
		} else {
			Ajde_Log::log("Wedeal::updatePayment() failed because: " . $res['response']);
			return false;
		}
	}
	
	// Helpers	
	
	private function sendRequest($request, $asRaw = false) {
		
		if (self::$_debug) {
			Ajde_Log::log("INPUT DATA: " . var_export($request, true));
		}
		
		$xml = self::buildXML($request);		
		$url = fsockopen ("ssl://".self::$_api_url, 443);
		
		if ($url === false) {
			return array(
				'success' => false,
				'response' => 'iDeal foutmelding: Kan niet verbinden'
			);
		}
	
	    $data = $xml->saveXML();
	    $length = strlen ($data);
		
		if (self::$_debug) {
			Ajde_Log::log("REQUEST XML: " . var_export($data, true));
		}
	
	    $post = "GET " . self::$_api_path . " HTTP/1.0\n";
	    $post .= "Content-Length: $length\n";
	    $post .= "Content-Type: text/xml\n";
	    $post .= "Connection: Close\n\n";
	    $post .= "$data\n\n";
	
	    fputs ($url, $post);
	    
		$response = '';
		while (!feof($url)) {
			$response .= fgets($url, 1024);
		}
		
		fclose ($url);
		
		if (self::$_debug) {
			Ajde_Log::log("RESPONSE DATA: " . var_export($response, true));
		}
		
		if ($asRaw) {
			
			$contentLenght = strpos($response, PHP_EOL . 'Content-Length:') + 1;
			$nextLine = strpos($response, PHP_EOL, $contentLenght);
			$result = trim(substr($response, $nextLine));
			
		} else {
		
			if (strpos($response, "<?xml") === false) {
				return array(
					'success' => false,
					'response' => "iDeal foutmelding: Ongeldig antwoord"
				);
			}

			$start = strpos($response, '<?xml');
			$response = substr($response, $start);

			$xml = new DOMDocument();
			$xml->loadXML($response);

			$result = simplexml_import_dom($xml);

			if (self::$_debug) {
				Ajde_Log::log("OUTPUT XML: " . var_export($result, true));
			}
			
			if ($result->error) {
				return array(
					'success' => false,
					'response' => "iDeal foutmelding ($result->error): " . self::getError($result->error)
				);				
			}
		}
	    	
		return array(
			'success' => true,
			'response' => $result
		);	
	}
	
	private static function getError($errno) {
		switch ($errno) {
			case "0000":
				return "Onbekend bericht";
			case "0100":
				return "Test-omgeving is niet geactiveerd";
			case "0200":
				return "Live-omgeving is niet geactiveerd";
			case "1100":
				return "Banklist aanvraag mislukt";
			case "2000":
				return "Transactie open";
			case "2100":
				return "Transactie succesvol";
			case "2200":
				return "Transactie mislukt";
			case "2300":
				return "Transactie verlopen";
			case "2400":
				return "Transactie niet gevonden";
			case "2500":
				return "Bedrag is te laag. Het minimum zijn uw transactiekosten.";
			case "3000":
				return "Login succesvol";
			case "3100":
				return "Merchant bestaat niet";
			case "3200":
				return "Wachtwoord onjuist";
			case "3300":
				return "Merchant is geblokkeerd";
			case "4000":
				return "Het bedrag is niet in centen. Gebruik dus geen , of .";
			case "9000":
				return "SSL niet gebruikt, gebruik HTTPS";
		}
	}
	
	private static function isPaid($status) {
		$status = strtolower($status);
		if ($status == 'register' || $status == 'processing' || $status == 'cancelled' || $status == 'failed') {
			return false;
		} else {
			return true;
		}
	}
	
	private static function isRefused($status) {
		$status = strtolower($status);
		if ($status == 'cancelled' || $status == 'failed') {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * @param $request
	 * @return DOMDocument
	 */
	private static function buildXML($request) {
		$xml = new DOMDocument();
		$reqelm = $xml->createElement("request");
		self::appendData($reqelm, $xml, $request);
		$xml->appendChild($reqelm);
		return $xml;
	}
	
	private static function appendData(&$element, $xml, $data)
	{
		foreach($data as $k => $v) {
			if (is_array($v)) {
				$child = $xml->createElement($k);
				self::appendData($child, $xml, $v);
			} else {
				$child = $xml->createElement($k, $v);				
			}
			$element->appendChild($child);
		}
	}
}