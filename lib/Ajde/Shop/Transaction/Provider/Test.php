<?php

class Ajde_Shop_Transaction_Provider_Test extends Ajde_Shop_Transaction_Provider
{
    public function getName() {
        return 'Test';
    }

    public function getLogo() {
        return MEDIA_DIR . '_core/shop/test.png';
    }

    public function usePostProxy() {
        return false;
    }

    public function getRedirectUrl($description = null)
    {
        return Config::get('site_root') . 'shop/transaction:test?txn=' . $this->getTransaction()->getPK();
    }

    public function getRedirectParams($description = null) {
        return array();
    }

    public function updatePayment()
    {
        $txn_id = $_GET['txn'];
        $transaction = new TransactionModel();
        $transaction->loadByPK($txn_id);

        $result = !!$_GET['r'];

        if ($result) {
            $transaction->payment_details = 'paid with test';
            $transaction->paid();

            return array(
                'success' => true,
                'changed' => true,
                'transaction' => $transaction
            );
        } else {
            $transaction->payment_status = 'refused';
            $transaction->save();

            return array(
                'success' => false,
                'changed' => true,
                'transaction' => $transaction
            );
        }
    }
}