<?php 

class AdminShopController extends AdminController
{
    public function menu()
    {
        return $this->render();
    }

    public function products()
    {
        Ajde::app()->getDocument()->setTitle("Product catalogue");
        return $this->render();
    }

	public function carts()
	{
		Ajde::app()->getDocument()->setTitle("Carts overview");
		return $this->render();
	}

    public function transactions()
    {
        Ajde::app()->getDocument()->setTitle("Transactions overview");
        return $this->render();
    }

    public static function getTransactionOptions()
    {
        $options = new Ajde_Crud_Options();
        $options
            ->selectFields()
                ->selectField('name')
                    ->setEmphasis(true)
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
                    ->setFunction('getFormattedTotal')
                    ->setLabel('Payment total')
                    ->up()
                ->selectField('shipment_cost')
                    ->setLabel('Shipment costs incl. VAT')
                    ->up()
                ->selectField('shipment_itemstotal')
                    ->setLabel('Total incl. VAT')
                    ->up()
                ->selectField('shipment_address')
                    ->setDisableRichText(true)
                    ->setLength(0)
                    ->up()
                ->up()
            ->selectList()
                ->selectButtons()
                    ->setEdit(false)
                    ->setNew(false)
                    ->setView(true)
                    ->up()
                ->setMain('name')
                ->setShow(array('name', 'added', 'comment', 'payment_provider', 'payment_amount', 'payment_status', 'shipment_method'))
                ->selectView()
                    ->setOrderBy('modified')
                    ->setOrderDir('DESC')
                    ->up()
                ->up()
            ->selectEdit()
                ->setIsReadonly(true)
                ->selectLayout()
                    ->addRow()
                        ->addColumn()
                            ->setSpan(8)
                            ->addBlock()
                                ->setTitle('Order')
                                ->setShow(array('name', 'added', 'comment', 'shipment_description'))
                                ->up()
                            ->addBlock()
                                ->setTitle('User')
                                ->setShow(array('email', 'user', 'ip'))
                                ->up()
                            ->addBlock()
                                ->setTitle('Payment')
                                ->setShow(array('payment_provider', 'payment_status', 'payment_details', 'payment_providerid'))
                                ->up()
                            ->addBlock()
                                ->setTitle('Shipment')
                                ->setShow(array('shipment_address', 'shipment_zipcode', 'shipment_city', 'shipment_region', 'shipment_country', 'shipment_status', 'shipment_method', 'shipment_trackingcode'))
                                ->up()
                            ->up()
                        ->addColumn()
                            ->setSpan(4)
                            ->addBlock()
                                ->setTitle('Summary')
                                ->setShow(array('shipment_itemsqty', 'shipment_itemsvatamount', 'shipment_itemstotal', 'shipment_cost', 'payment_amount'))
                                ->up()
                            ->addBlock()
                                ->setTitle('Metadata')
                                ->setClass('sidebar well')
                                ->setShow(array('extra', 'secret', 'secret_archive', 'modified'))
                                ->up()

            ->finished();

        return $options;
    }
}