<?php
require "Mollie/API/Autoloader.php";

abstract class Ajde_Shop_Transaction_Provider_Mollie extends Ajde_Shop_Transaction_Provider
{
    abstract protected function getMethod();

    public function getName() {
        return 'Mollie';
    }

    public function getLogo() {
        return MEDIA_DIR . '_core/shop/mollie.png';
    }

    public function usePostProxy() {
        return false;
    }

    public function getRedirectUrl($description = null)
    {
        $transaction = $this->getTransaction();

        $mollie = new Mollie_API_Client;
        $mollie->setApiKey($this->getApiKey());

        $order_id = $transaction->secret;
        $data = array(
            "amount"       => $transaction->payment_amount,
            "description"  => isset($description) ? $description : Config::get('sitename') . ': ' . Ajde_Component_String::makePlural($transaction->shipment_itemsqty, 'item'),
            "redirectUrl"  => Config::get('site_root') . $this->returnRoute . 'mollie_' . $this->getMethod() . '.html?order_id=' . $order_id,
            "method"       => $this->getMethod(),
            "metadata"     => array(
                "order_id" => $order_id,
            ),
        );

        $payment = $mollie->payments->create($data);

        // save details
        $transaction->payment_providerid = $payment->id;
        $transaction->save();

        return $payment->getPaymentUrl();
    }

    public function getRedirectParams($description = null) {
        return array();
    }

    public function updatePayment()
    {
        $payment = false;

        $mollie = new Mollie_API_Client;
        $mollie->setApiKey($this->getApiKey());

        $transaction = new TransactionModel();

        // see if we are here for the webhook or user return url
        $mollie_id = Ajde::app()->getRequest()->getPostParam('id', false); // from webhook
        $order_id = Ajde::app()->getRequest()->getParam('order_id', false); // from user request

        if (!$mollie_id && $order_id) {
            // load from order_id
            $transaction->loadByField('secret', $order_id);
            $mollie_id = $transaction->payment_providerid;
            try {
                $payment = $mollie->payments->get($mollie_id);
            } catch(Mollie_API_Exception $e) {
                Ajde_Exception_Log::logException($e);
                $payment = false;
            }
        } else if ($mollie_id) {
            // laod from mollie transaction id
            try {
                $payment = $mollie->payments->get($mollie_id);
                $order_id = $payment->metadata->order_id;
                $transaction->loadByField('secret', $order_id);
            } catch(Mollie_API_Exception $e) {
                Ajde_Exception_Log::logException($e);
                $payment = false;
            }
        }

        if (!$payment || !$mollie_id || !$order_id || !$transaction->hasLoaded()) {
            Ajde_Log::log('Could not find transaction for Mollie payment for mollie id ' . $mollie_id . ' and transaction secret ' . $order_id);
            return array(
                'success' => false,
                'transaction' => $transaction
            );
        }

        // what to return?
        $paid = false;

        // save details
        $details =
            'PAYMENT STATUS: '		    . (string) $payment->status             . PHP_EOL .
            'PAYMENT AMOUNT: '          . (string) $payment->amount             . PHP_EOL .
            'PAYMENT AT: '              . (string) $payment->paidDatetime       . PHP_EOL .
            'CANCELLED AT: '            . (string) $payment->cancelledDatetime  . PHP_EOL .
            'EXPIRED AT: '              . (string) $payment->expiredDatetime    . PHP_EOL .
            'PAYER DETAILS: '		    . (string) $payment->details;
        $transaction->payment_details = $details;

        switch($payment->status) {
            case "open":
                $transaction->payment_status = 'requested';
                $transaction->save();
                $transaction = null;
                break;
            case "paidout":
            case "paid":
                $paid = true;
                // update transaction only once
                if ($transaction->payment_status != 'completed') {
                    $transaction->payment_status = 'completed';
                    $transaction->save();
                } else {
                    $transaction = null;
                }
                break;
            case "cancelled":
                // update transaction only once
                if ($transaction->payment_status != 'cancelled') {
                    $transaction->payment_status = 'cancelled';
                    $transaction->save();
                } else {
                    $transaction = null;
                }
                break;
            case "expired":
                // update transaction only once
                if ($transaction->payment_status != 'refused') {
                    $transaction->payment_status = 'refused';
                    $transaction->save();
                } else {
                    $transaction = null;
                }
                break;
        }

        return array(
            'success' => $paid,
            'transaction' => $transaction
        );
    }

    private function getApiKey()
    {
        return $this->isSandbox() ? Config::get('shopMollieTestKey') : Config::get('shopMollieLiveKey');
    }
}