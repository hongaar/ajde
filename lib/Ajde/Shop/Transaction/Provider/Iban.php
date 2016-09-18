<?php

class Ajde_Shop_Transaction_Provider_Iban extends Ajde_Shop_Transaction_Provider
{
    public function getName()
    {
        return trans('IBAN bank transfer', 'shop');
    }

    public function getLogo()
    {
        return MEDIA_DIR.'core/shop/iban.png';
    }

    public function usePostProxy()
    {
        return false;
    }

    public function getRedirectUrl($description = null)
    {
        return config('app.rootUrl').'shop/transaction:iban?txn='.$this->getTransaction()->getPK();
    }

    public function getRedirectParams($description = null)
    {
        return [];
    }

    public function updatePayment()
    {
        $txn_id = $_GET['txn'];
        $transaction = new TransactionModel();
        $transaction->loadByPK($txn_id);

        $result = (bool) $_GET['r'];

        if ($result) {
            $transaction->payment_status = 'requested';
            $transaction->save();

            return [
                'success'     => true,
                'changed'     => true,
                'transaction' => $transaction,
            ];
        } else {
            return [
                'success'     => false,
                'changed'     => true,
                'transaction' => $transaction,
            ];
        }
    }
}
