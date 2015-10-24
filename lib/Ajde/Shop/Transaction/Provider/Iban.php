<?php

class Ajde_Shop_Transaction_Provider_Iban extends Ajde_Shop_Transaction_Provider
{
    public function getName() {
        return __('IBAN bank transfer', 'shop');
    }

    public function getLogo() {
        return MEDIA_DIR . '_core/shop/iban.png';
    }

    public function usePostProxy() {
        return false;
    }

    public function getRedirectUrl($description = null)
    {
        return Config::get('site_root') . 'shop/transaction:iban?txn=' . $this->getTransaction()->getPK();
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
            $transaction->payment_status = 'requested';
            $transaction->save();

            return array(
                'success' => true,
                'changed' => true,
                'transaction' => $transaction
            );
        } else {
            return array(
                'success' => false,
                'changed' => true,
                'transaction' => $transaction
            );
        }
    }
}