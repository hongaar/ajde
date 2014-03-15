<?php 
require_once 'ShopController.php';

class ShopCartController extends ShopController
{	
	protected $_allowedActions = array(
		'view',
		'edit',
		'add',
		'widget',
		'switchUser'
	);
	
	public function view()
	{
		$this->redirect('shop/cart:edit');
	}
	
	public function edit()
	{
		Ajde_Model::register($this);
		$cart = new CartModel();
		$cart->loadCurrent();
		
		if (Ajde::app()->getRequest()->hasPostParam('update')) {
			$qtyArray = Ajde::app()->getRequest()->getPostParam('qty');
			$item = new CartItemModel();
			foreach($qtyArray as $itemId => $qty) {
				$item->loadByPK($itemId);
				if ($item->cart == (string) $cart) {
					if ((int) $qty == 0) {
						$item->delete();
					} else {
						$item->qty = $qty;
						$item->save();
					}
				}
				$item->reset();
			}
		}
		
		$items = $cart->getItems();
		
		$this->getView()->assign('items', $items);
		return $this->render();
	}
	
	public function widget()
	{
		Ajde_Model::register($this);
		$cart = new CartModel();
		$cart->loadCurrent();
		$items = $cart->getItems();
		
		// Prevent caching when called in body format
		$this->touchCache($items);
		
		$this->getView()->assign('quickcheckout', $this->hasId() && ($this->getId() == 'quickcheckout'));
		$this->getView()->assign('items', $items);
		return $this->render();
	}
	
	public function switchUser()
	{
		Ajde_Shop_Cart_Merge::mergeUserToClient();				
		$user = $this->getLoggedInUser();
		$user->logout();		
		$this->redirect('user/logon?returnto=' . Ajde::app()->getRequest()->getParam('returnto', ''));
	}
	
	public function addHtml()
	{
		Ajde_Model::register($this);
		$cart = new CartModel();
		$cart->loadCurrent();
		
		$item = $this->_getFingerprint($this->getId());
		
		$view = $this->getView();		
		$view->assign('entity', $item['entity']);
		$view->assign('entity_id', $item['id']);
		return $this->render();
	}
	
	public function addJson()
	{
		Ajde_Model::register($this);
		$cart = new CartModel();
		$cart->loadCurrent();
		
		$entity = Ajde::app()->getRequest()->getPostParam('entity');
		$entity_id = Ajde::app()->getRequest()->getPostParam('entity_id');
		$qty = Ajde::app()->getRequest()->getPostParam('qty');
		
		$cart->addItem($entity, $entity_id, $qty);
		
		return array('success' => true);
	}

	private function _getFingerprint($id)
	{
		if (substr_count($id, ':') === 0) {
			throw new Ajde_Controller_Exception('ID must contain a \':\' when calling _getFingerprint()');
		}
		$array = explode(':', $id);
		return array(
			'entity'	=> $array[0],
			'id'		=> $array[1]
		);
	}
	
}
