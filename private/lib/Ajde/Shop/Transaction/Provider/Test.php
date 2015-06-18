<?php

class Ajde_Shop_Transaction_Provider_Test extends Ajde_Shop_Transaction_Provider
{
    public function getName() {
        return 'Test';
    }

    public function getLogo() {
        return 'https://lh4.ggpht.com/wKrDLLmmxjfRG2-E-k5L5BUuHWpCOe4lWRF7oVs1Gzdn5e5yvr8fj-ORTlBF43U47yI=w75';
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