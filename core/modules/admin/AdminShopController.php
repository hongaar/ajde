<?php

class AdminShopController extends AdminController
{
    public function menu()
    {
        return $this->render();
    }

    public function products()
    {
        Ajde::app()->getDocument()->setTitle('Product catalogue');

        return $this->render();
    }

    public function carts()
    {
        Ajde::app()->getDocument()->setTitle('Carts overview');

        return $this->render();
    }

    public function transactions()
    {
        Ajde::app()->getDocument()->setTitle('Transactions overview');

        return $this->render();
    }

    public function markPaidJson()
    {
        $id = Ajde::app()->getRequest()->getPostParam('id', false);

        $transaction = new TransactionModel();

        if (!is_array($id)) {
            $id = [$id];
        }

        $c = 0;

        foreach ($id as $elm) {
            $transaction->loadByPK($elm);
            if ($transaction->payment_status !== 'completed') {
                $transaction->paid();
                $c++;
            }
        }

        return [
            'success' => true,
            'message' => Ajde_Component_String::makePlural($c, 'transaction').' marked as paid',
        ];
    }

    public static function getTransactionOptions()
    {
        $options = new Ajde_Crud_Options();
        $options
            ->selectFields()
            ->selectField('id')
            ->setLabel('Order ID')
            ->setFunction('displayOrderId')
            ->up()
            ->selectField('name')
            ->setEmphasis(true)
            ->up()
            ->selectField('added')
            ->setIsReadonly(true)
            ->up()
            ->selectField('user')
            ->setIsReadonly(true)
            ->up()
            ->selectField('ip')
            ->setIsReadonly(true)
            ->up()
            ->selectField('payment_provider')
            ->setIsReadonly(true)
            ->up()
            ->selectField('payment_details')
            ->setIsReadonly(true)
            ->up()
            ->selectField('payment_providerid')
            ->setIsReadonly(true)
            ->up()
            ->selectField('shipment_description')
            ->setIsReadonly(true)
            ->up()
            ->selectField('secret')
            ->setIsReadonly(true)
            ->up()
            ->selectField('secret_archive')
            ->setDisableRichText(true)
            ->setIsReadonly(true)
            ->up()
            ->selectField('extra')
            ->setDisableRichText(true)
            ->setLength(255)
            ->up()
            ->selectField('comment')
            ->setDisableRichText(true)
            ->setIsReadonly(true)
            ->setLength(0)
            ->up()
            ->selectField('payment_details')
            ->setDisableRichText(true)
            ->up()
            ->selectField('payment_providerid')
            ->setDisableRichText(true)
            ->setLength(255)
            ->up()
            ->selectField('shipment_trackingcode')
            ->setDisableRichText(true)
            ->setLength(255)
            ->up()
            ->selectField('payment_amount')
            ->setIsReadonly(true)
            ->setFunction('getFormattedTotal')
            ->setLabel('Payment total')
            ->up()
            ->selectField('shipment_cost')
            ->setIsReadonly(true)
            ->setLabel('Shipment costs incl. VAT')
            ->up()
            ->selectField('shipment_itemstotal')
            ->setIsReadonly(true)
            ->setLabel('Total incl. VAT')
            ->up()
            ->selectField('shipment_address')
            ->setDisableRichText(true)
            ->setLength(0)
            ->up()
            ->selectField('shipment_itemsqty')
            ->setIsReadonly(true)
            ->up()
            ->selectField('shipment_itemsvatamount')
            ->setIsReadonly(true)
            ->up()
            ->up()
            ->selectList()
            ->selectButtons()
            ->setEdit(true)
            ->setNew(false)
            ->setView(false)
            ->addToolbarButton('paid', 'mark as paid', 'paid itemAction btn-success')
            ->addItemButton('link', '<i class="icon-share-alt icon-white"></i>', 'link btn-primary')
            ->up()
            ->setMain('name')
            ->setShow(['id', 'name', 'comment', 'payment_provider', 'payment_amount', 'payment_status', 'added'])
            ->selectView()
            ->setOrderBy('id')
            ->setOrderDir('DESC')
            ->up()
            ->up()
            ->selectEdit()
            //                ->setIsReadonly(true)
            ->selectLayout()
            ->addRow()
            ->addColumn()
            ->setSpan(8)
            ->addBlock()
            ->setTitle('Order')
            ->setShow(['name', 'added', 'comment', 'shipment_description'])
            ->up()
            ->addBlock()
            ->setTitle('User')
            ->setShow(['email', 'user', 'ip'])
            ->up()
            ->addBlock()
            ->setTitle('Payment')
            ->setShow(['payment_provider', 'payment_status', 'payment_details', 'payment_providerid'])
            ->up()
            ->addBlock()
            ->setTitle('Shipment')
            ->setShow([
                'shipment_address',
                'shipment_zipcode',
                'shipment_city',
                'shipment_region',
                'shipment_country',
                'shipment_status',
                'shipment_method',
                'shipment_trackingcode',
            ])
            ->up()
            ->up()
            ->addColumn()
            ->setSpan(4)
            ->addBlock()
            ->setTitle('Summary')
            ->setShow([
                'shipment_itemsqty',
                'shipment_itemsvatamount',
                'shipment_itemstotal',
                'shipment_cost',
                'payment_amount',
            ])
            ->up()
            ->addBlock()
            ->setTitle('Metadata')
            ->setClass('sidebar well')
            ->setShow(['extra', 'secret', 'secret_archive', 'modified'])
            ->up()
            ->finished();

        return $options;
    }
}
