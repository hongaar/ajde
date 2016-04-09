<?php

class App extends Ajde_Object_Singleton implements Ajde_BootstrapInterface
{
    public static function getInstance()
    {
        static $instance;

        return $instance === null ? $instance = new self : $instance;
    }

    public function __bootstrap()
    {
        \Nabble\SemaltBlocker\Blocker::protect();

        Ajde_Event::register('TransactionModel', 'onPaid', [$this, 'onTransactionPaid']);
        Ajde_Event::register('TransactionModel', 'onCreate', [$this, 'onTransactionCreated']);

        if (UserModel::isTester() || UserModel::isAdmin()) {
            $providers   = Config::get('transactionProviders');
            $providers[] = 'test';
            Config::set('transactionProviders', $providers);
        }

        return true;
    }

    public function onTransactionPaid(TransactionModel $transaction)
    {
        /** @var TransactionItemModel $item */
        foreach ($transaction->getItems() as $item) {
            $entity = $item->getEntity();
            $qty    = $item->qty;

            if ($entity instanceof ProductModel) {
                $entity->stock = $entity->stock - $qty;
                $entity->save();
            }
        }
    }

    public function onTransactionCreated(TransactionModel $transaction)
    {
        $transaction->shipment_country = 'Nederland';
    }
}
