<?php
require_once 'ShopController.php';

class ShopTransactionController extends ShopController
{
    public function view()
    {
        $transaction = new TransactionModel();

        Ajde::app()->getDocument()->setTitle(__('Your order'));

        // Get from ID
        if ($this->hasNotEmpty('id')) {
            if ($transaction->loadByField('secret', $this->getId()) !== false) {
                $this->getView()->assign('source', 'id');
            }
        } else  {
            // Get existing transaction / user details
            $session = new Ajde_Session('AC.Shop');
            if ($session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'))) {
                $this->getView()->assign('source', 'session');
            }
        }

        $this->getView()->assign('transaction', $transaction);
        return $this->render();
    }

    public function export()
    {
        $transaction = new TransactionModel();

        // Get from ID
        if (!($this->hasNotEmpty('id') && $transaction->loadByField('secret', $this->getId()) !== false))
        {
            Ajde::app()->getResponse()->redirectNotFound();
        }

        /** @var Ajde_Document_Format_Generated $doc */
        $doc = Ajde::app()->getDocument();
        $url = Config::get('site_root') . 'shop/transaction:view/' . $this->getId() . '.html';
        $filename = $transaction->getOrderId();

        if ($this->getFormat() === 'pdf')
        {
            $pdf = $doc->generate(array(
                'url' => $url,
                'filename' => $filename
            ));
            $doc->setContentType('application/pdf');
            Ajde::app()->getResponse()->addHeader('Content-Disposition', 'attachment; filename="' . $filename . '.pdf"');

            return $pdf;
        }
    }

    public function setup()
    {
        // Get existing transaction / user details
        $transaction = new TransactionModel();
        $session = new Ajde_Session('AC.Shop');
        $user = UserModel::getLoggedIn();

        if ($session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'))) {
            $name		= $transaction->name;
            $email		= $transaction->email;
            $address	= $transaction->shipment_address;
            $zipcode	= $transaction->shipment_zipcode;
            $city		= $transaction->shipment_city;
            $region		= $transaction->shipment_region;
            $country	= $transaction->shipment_country;
            $comment	= $transaction->comment;
        } elseif ($user !== false) {
            // Insert intermediate transaction to save country and present user
            // with shipping options, ignore response
            $this->setupJson($user);

            $name		= $user->fullname;
            $email		= $user->email;
            $address	= $user->address;
            $zipcode	= $user->zipcode;
            $city		= $user->city;
            $region		= $user->region;
            $country	= $user->country;
            $comment	= '';
        } else {
            // Insert intermediate transaction to save cart and allow user to
            // see shipping options when country is choosen
            $this->setupJson(true);

            $name		= '';
            $email		= '';
            $address	= '';
            $zipcode	= '';
            $city		= '';
            $region		= '';
            $country	= '';
            $comment	= '';
        }

        $view = $this->getView();
        $view->assign('name', $name);
        $view->assign('email', $email);
        $view->assign('address', $address);
        $view->assign('zipcode', $zipcode);
        $view->assign('city', $city);
        $view->assign('region', $region);
        $view->assign('country', $country);
        $view->assign('comment', $comment);
        $view->assign('user', $user);
        return $this->render();
    }

    public function shipment()
    {
        $transaction = new TransactionModel();

        $session = new Ajde_Session('AC.Shop');
        if ($session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'))) {
            if (Ajde::app()->getRequest()->has('country')) {
                $transaction->shipment_country = Ajde::app()->getRequest()->getParam('country');
                $transaction->save();
            }
            $shipment = new ShippingModel($transaction);
            $method = $transaction->shipment_method;
            if (empty($method) || !$shipment->isAvailable($method)) {
                $method = $shipment->getFirstMethod()->getName();
            }
        } else {
            $shipment = false;
            $method = false;
            $transaction = false;
        }

        $this->getView()->assign('method', $method);
        $this->getView()->assign('shipment', $shipment);
        $this->getView()->assign('transaction', $transaction);
        return $this->render();
    }

    public function setupJson($source = false)
    {
        $request		= Ajde::app()->getRequest();

        // Init vars
        $name = null; $email = null; $address = null; $zipcode = null; $city = null; $region = null; $country = null; $shipmentMethod = null; $comment = null;

        if ($source === false) {
            // Read request
            $name			= $request->getPostParam('name', false);
            $email			= $request->getPostParam('email', false);
            $address		= $request->getPostParam('shipment_address', false);
            $zipcode		= $request->getPostParam('shipment_zipcode', false);
            $city			= $request->getPostParam('shipment_city', false);
            $region			= $request->getPostParam('shipment_region', false);
            $country		= $request->getPostParam('shipment_country', false);
            $shipmentMethod	= $request->getPostParam('shipment_method', false);
            $comment		= $request->getPostParam('comment', false);
        } else if ($source instanceof Ajde_User) {
            // Read user
            $name			= $source->fullname;
            $email			= $source->email;
            $address		= $source->address;
            $zipcode		= $source->zipcode;
            $city			= $source->city;
            $region			= $source->region;
            $country		= $source->country;
            $shipmentMethod	= false;
            $comment		= false;
        }

        // Return when fields are not complete
        if ($source === false) {
            if (
                empty($name) ||
                empty($email) ||
                empty($address) ||
                empty($zipcode) ||
                empty($city) ||
                empty($country)
            ) {
                return array(
                    'success' => false,
                    'message' => __("Not all your details are filled out")
                );
            }
            if (Ajde_Component_String::validEmail($email) === false) {
                return array(
                    'success' => false,
                    'message' => __('Please provide a valid e-mail address')
                );
            }
            if (empty($shipmentMethod)) {
                return array(
                    'success' => false,
                    'message' => __('Please choose a shipment method')
                );
            }
        }

        // Check for current transaction
        $transaction = new TransactionModel();
        $session = new Ajde_Session('AC.Shop');
        if ($session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'))) {
            // Edit existing transaction
            $method = 'save';
        } else {
            // Insert new transaction
            $method = 'insert';
        }

        // Update transaction info
        $transaction->name				= $name;
        $transaction->email				= $email;
        $transaction->shipment_address	= $address;
        $transaction->shipment_zipcode	= $zipcode;
        $transaction->shipment_city		= $city;
        $transaction->shipment_region	= $region;
        $transaction->shipment_country	= $country;
        $transaction->shipment_method	= $shipmentMethod;
        $transaction->comment			= $comment;

        // Save info to user
        if ($user = $this->getLoggedInUser()) {
            if ($request->hasPostParam('save_details', false)) {
                $user->address	= $address;
                $user->zipcode	= $zipcode;
                $user->city		= $city;
                $user->region	= $region;
                $user->country	= $country;
                $user->save();
                $user->login();
            }
            $transaction->user = $user->getPK();
        }

        // Update shipping total
        $shipping = new ShippingModel($transaction);
        $transaction->shipment_cost = 0;
        if (!empty($shipmentMethod) && $shipping->isAvailable($shipmentMethod)) {
            $transaction->shipment_cost = $shipping->getMethod($shipmentMethod)->getTotal();
        }

        // Insert new transaction
        if ($method === 'insert') {
            if ($transaction->insert()) {
                $this->updateFromCart($transaction);
                $session = new Ajde_Session('AC.Shop');
                $session->set('currentTransaction', $transaction->getPK());
                if (!$transaction->shipment_itemsqty > 0) {
                    return array(
                        'success' => false,
                        'message' => __("No items added to current order")
                    );
                }
                return array(
                    'success' => true
                );
            }
            return array(
                'success' => false,
                'message' => __("Something went wrong")
            );
        }

        $transaction->payment_amount = $transaction->shipment_itemstotal + $transaction->shipment_cost;

        // Update current transaction
        if ($transaction->save()) {
            if (!$transaction->shipment_itemsqty > 0) {
                return array(
                    'success' => false,
                    'message' => __("No items added to current transaction")
                );
            }
            return array(
                'success' => true
            );
        }

        // Everything else failed
        return array(
            'success' => false,
            'message' => __("Something went wrong")
        );
    }

    public function updateFromCart(Ajde_Shop_Transaction $transaction)
    {
        $cart = new CartModel();
        $cart->loadCurrent();

        if ($cart->countItems() > 0) {
            $transaction->shipment_description		= $cart->getHtmlSummaryTable();
            $transaction->shipment_itemsqty			= $cart->countQty();
            $transaction->shipment_itemsvatamount	= $cart->getItems()->getVATAmount();
            $transaction->shipment_itemstotal		= $cart->getItems()->getTotal();
            $transaction->payment_amount			= $transaction->shipment_itemstotal + $transaction->shipment_cost;
        } else {
            $transaction->shipment_description		= '';
            $transaction->shipment_itemsqty			= 0;
            $transaction->shipment_itemsvatamount	= 0;
            $transaction->shipment_itemstotal		= 0;
            $transaction->payment_amount			= 0;
        }

        $transaction->setItemsFromCart($cart);
        $transaction->save();
    }

    public function update()
    {
        // Check for current transaction
        $transaction = new TransactionModel();
        $session = new Ajde_Session('AC.Shop');
        if ($session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'))) {
            $this->updateFromCart($transaction);
        }
        Ajde_Session_Flash::alert(__('Your order has been updated', 'shop'));
        $this->redirect('shop/transaction:setup');
//		$this->setAction('view');
//		return $this->view();
    }

    public function cancel()
    {
        // Edit existing transaction?
        $transaction = new TransactionModel();
        $session = new Ajde_Session('AC.Shop');
        if ($session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'))) {
            $transaction->payment_status = 'cancelled';
            $transaction->save();
            $session->destroy();
        }
        Ajde_Session_Flash::alert(__('Your order has been cancelled', 'shop'));
        $this->redirect('shop');
    }

    public function payment()
    {
        $transaction = new TransactionModel();

        $session = new Ajde_Session('AC.Shop');
        if ($session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'))) {
            if (!$transaction->shipment_itemsqty > 0) {
                $this->redirect('shop');
            }
        }

        $this->getView()->assign('transaction', $transaction);
        return $this->render();
    }

    public function resetPayment()
    {
        $transaction = new TransactionModel();
        $session = new Ajde_Session('AC.Shop');

        // Get transaction from ID if available
        if ($this->hasNotEmpty('id')) {
            if ($transaction->loadByField('secret', $this->getId()) !== false) {
                $session->set('currentTransaction', $transaction->getPK());
            }
        }

        if ($session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'))) {
            $transaction->payment_provider = null;
            $transaction->payment_status = 'pending';
            $transaction->secret_archive = $transaction->secret_archive . $transaction->secret . PHP_EOL;
            $transaction->secret = $transaction->generateSecret();
            $transaction->save();
        }

        $this->redirect('shop/transaction:payment');
    }

    public function paymentJson()
    {
        $request = Ajde::app()->getRequest();
        $provider = $request->getPostParam('provider', false);

        if (empty($provider)) {
            return array(
                'success' => false,
                'message' => __('Please choose a payment provider')
            );
        }

        // Check for current transaction
        $transaction = new TransactionModel();
        $session = new Ajde_Session('AC.Shop');
        if ($session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'))) {
            if ($transaction->payment_status !== 'pending') {
                return array(
                    'success' => false,
                    'message' => __('Payment already initiated, please refresh this page')
                );
            }
        } else {
            return array(
                'success' => false,
                'message' => __('No current order found')
            );
        }

        $transaction->payment_provider = $provider;

        $provider = $transaction->getProvider();
        $redirectUrl = $provider->getRedirectUrl();

        if ($redirectUrl !== false) {

            $transaction->payment_status = 'requested';
            $transaction->save();

            if ($provider->usePostProxy()) {
                $this->setAction('postproxy');
                $proxy = $this->getView();
                $proxy->assign('provider', $provider);
                return array(
                    'success' => true,
                    'postproxy' => $proxy->render()
                );
            }

            return array(
                'success' => true,
                'redirect' => $redirectUrl
            );
        }

        return array(
            'success' => false,
            'message' => 'Could not contact the payment provider, please try again'
        );
    }

    public function complete()
    {
        $cart = new CartModel();
        $cart->loadCurrent();
        $cart->emptyItems();

        // Get existing transaction
        $transaction = new TransactionModel();
        $session = new Ajde_Session('AC.Shop');
        if ($session->has('currentTransaction')) {
            $transaction->loadByPK($session->get('currentTransaction'));
        }

        $session->destroy();

        $this->getView()->assign('transaction', $transaction);
        return $this->render();
    }

    public function refused()
    {
        return $this->render();
    }

    public function callback()
    {
        $providerName = $this->getId();
        $provider = Ajde_Shop_Transaction_Provider::getProvider($providerName);
        $status = $provider->updatePayment();
        if ($status['success'] === true) {
            $transaction = $status['transaction'];
            if (isset($transaction)) {
                $this->mailUser($transaction);
                $this->mailUpdateAdmin($transaction, 'Order completed');
            }
            $this->redirect('shop/transaction:complete');
        } else {
            $transaction = $status['transaction'];
            if (isset($transaction)) {
                $this->mailUpdateAdmin($transaction, 'Order refused');
            }
            $this->redirect('shop/transaction:refused');
        }
    }

    public function startNew()
    {
        $session = new Ajde_Session('AC.Shop');
        $session->destroy();

        return $this->redirect('shop/cart');
    }

    public function mailUpdateAdmin(TransactionModel $transaction, $subject = null)
    {
        $recipient = Config::get('email');

        $mailer = new Ajde_Mailer();
        $mailer->SendQuickMail($recipient, $recipient, Config::get('sitename'), isset($subject) ? $subject : 'Order update', $transaction->getOverviewHtml());
    }

    public function mailUser(TransactionModel $transaction)
    {
        $viewLink = Config::get('site_root') . 'shop/transaction:view/' . $transaction->secret . '.html';

        $mailer = new Ajde_Mailer();
        $mailer->sendUsingModel('your_order', $transaction->email, $transaction->name, array(
            'viewlink' => $viewLink
        ));
    }

    /**
     * @param TransactionItemModel $transaction
     * @deprecated use mailUser
     * @throws Ajde_Core_Exception_Deprecated
     * @throws Ajde_Exception
     * @throws Exception
     * @throws phpmailerException
     */
    public function mailUserDeprecated(TransactionItemModel $transaction)
    {
        throw new Ajde_Core_Exception_Deprecated();

        $mailer = new Ajde_Mailer();

        $mailer->IsMail(); // use php mail()
        $mailer->AddAddress($transaction->email, $transaction->name);
        $mailer->From = Config::get('email');
        $mailer->FromName = Config::get('sitename');
        $mailer->Subject = 'Your order';
        $mailer->Body = '<h2>Your order on ' . Config::get('sitename') . '</h2>' .
            '<p>Thank you for shopping with us. We will ship your items as soon as possible if you chose for delivery.<br/>' .
            'To view the status of your order, please click this link:</p>' .
            '<p><a href=\'' . Config::get('site_root') . 'shop/transaction:view/' . $transaction->secret . '.html\'>View your order status</a></p>' .
            '<p>Hope to welcome you again soon on <a href=\'' . Config::get('site_root') . '\'>' . Config::get('sitename') . '</a></p>';
        $mailer->IsHTML(true);
        if (!$mailer->Send()) {
            Ajde_Log::log('Mail to ' . $transaction->email . ' failed');
        }
    }

    public function test()
    {
        $this->setAction('test/confirm');
        return $this->render();
    }

    public function iban()
    {
        $this->setAction('iban/confirm');
        return $this->render();
    }
}
