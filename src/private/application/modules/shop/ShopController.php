<?php 

class ShopController extends Ajde_Acl_Controller
{
	protected $_allowedActions = array(
		'view'
	);
	
	protected $_allowGuestTransaction = true;
	
	public function beforeInvoke()
	{
		if ($this->_allowGuestTransaction === true) {
			$this->_allowedActions[] = $this->getAction();
		}
		Ajde_Cache::getInstance()->disable();
		return parent::beforeInvoke();
	}
	
	public function view()
	{
		return $this->redirect('samples');
	}
	
	public function cart()
	{
		$this->redirect('shop/cart:edit');
	}
	
	public function checkout()
	{
		Ajde_Model::register($this);
		
		// Get existing transaction
		$transaction = new TransactionModel();
		$session = new Ajde_Session('AC.Shop');				
		$session->has('currentTransaction') && $transaction->loadByPK($session->get('currentTransaction'));
				
		$cart = new CartModel();
		$cart->loadCurrent();
		
		$this->getView()->assign('cart', $cart);
		$this->getView()->assign('user', $this->getLoggedInUser());
		$this->getView()->assign('transaction', $transaction);
		
		return $this->render();
	}
}
