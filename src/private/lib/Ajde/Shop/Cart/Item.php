<?php

abstract class Ajde_Shop_Cart_Item extends Ajde_Model
{	
	protected $_autoloadParents = false;
	
	protected $_cartModel = null;
		
	public function __construct(Ajde_Shop_Cart $cart = null) {
		parent::__construct();
		if (isset($cart)) {
			$this->setCart($cart);
		}
	}
	
	public function hasLoaded() {
		return isset($this->_data['entity_id']) && !empty($this->_data['entity_id']);
	}
	
	/**
	 *
	 * @param Ajde_Shop_Cart $cart 
	 */
	public function setCart(Ajde_Shop_Cart $cart)
	{
		parent::setCart($cart);
	}
	
	/**
	 *
	 * @return Ajde_Shop_Cart
	 */
	public function getCart()
	{
		$cart = parent::getCart();
		if (!$cart instanceof Ajde_Shop_Cart) {
			$cartModel = $this->_getCartModel();
			$cartModel->loadByPK($cart);
			$cart = $cartModel;
			parent::setCart($cart);			
		}
		return $cart;
	}
	
	public function load($entity, $id)
	{
		return $this->loadByFields(array(
			'cart' => $this->getCart()->getPK(),
			'entity' => $entity,
			'entity_id' => $id));
	}
	
	public function getQty()
	{
		return (int) parent::getQty();
	}
	
	public function addQty($qty)
	{
		$this->setQty($this->getQty() + (int) $qty);
	}
	
	public function setEntityById($entityName, $id)
	{
		try {
			$entity = $this->_getEntityModel($entityName);
			$entity->loadByPK($id);
		} catch(Exception $e) {
			// TODO:
			throw new Ajde_Exception("Could not load entity $entity into cart");
		}
		$this->setEntity($entity);
	}
	
	public function setEntity(Ajde_Model $entity)
	{
		try {			
			$unitprice = $entity->getUnitprice();
		} catch(Exception $e) {
			// TODO:
			throw new Ajde_Exception("Entity $entity does not have a unitprice defined");
		}
		$this->set('entity', $entity->getTable());
		$this->set('entity_id', $entity);
		$this->set('unitprice', $unitprice);
	}
	
	/**
	 *
	 * @return Ajde_Model
	 */
	public function getEntity()
	{
		$entity = $this->get('entity_id');
		if (!$entity instanceof Ajde_Model)
		{
			$id = $entity;
			$entity = $this->_getEntityModel($this->get('entity'));
			$entity->loadByPK($id);
		}
		return $entity;
	}
	
	/**
	 *
	 * @param string $entity
	 * @return Ajde_Model
	 */
	protected function _getEntityModel($entityName)
	{
		Ajde_Model::registerAll();
		$entityModelName = ucfirst((string) $entityName) . 'Model';
		return new $entityModelName();
	}
	
	/**
	 *
	 * @return Ajde_Shop_Cart_Item
	 */
	protected function _getCartModel()
	{
		$cartModelName = $this->_cartModel;
		return new $cartModelName();
	}
	
	protected function _format($value)
	{
		return money_format('%!i', $value);
	}
	
	public function getFormattedUnitprice()
	{
		return Config::get('currency') . ' ' . $this->_format(($this->getVATPercentage()+1) * $this->getUnitprice());
	}
	
	public function getVATPercentage()
	{
		$entity = $this->getEntity();
		if ($entity->has('VATPercentage') || method_exists($entity, 'getVATPercentage')) {
			return $entity->getVATPercentage();
		}
		return Config::get('defaultVAT');
	}
	
	public function getVATAmount()
	{
		return $this->getVATPercentage() * $this->getSubTotal();
	}
	
	public function getFormattedVATAmount()
	{
		return Config::get('currency') . ' ' . $this->_format($this->getVATAmount());
	}
		
	public function getSubTotal()
	{
		return $this->getUnitprice() * $this->getQty();
	}
	
	public function getTotal()
	{
		return $this->getVATAmount() + $this->getSubTotal();
	}
	
	public function getFormattedTotal()
	{
		return Config::get('currency') . ' ' . $this->_format($this->getTotal());
	}
	
}