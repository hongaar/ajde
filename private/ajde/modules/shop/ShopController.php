<?php 

class ShopController extends Ajde_Acl_Controller
{
    /**
     *
     * @var ProductModel
     */
    protected $product;

	protected $_allowedActions = array(
		'view'
	);
	
	protected $_allowGuestTransaction = true;
	
	public function beforeInvoke($allowed = array())
	{
		if ($this->_allowGuestTransaction === true) {
			$this->_allowedActions[] = $this->getAction();
		}
		Ajde_Cache::getInstance()->disable();
		return parent::beforeInvoke();
	}

    public function getCanonicalUrl()
    {
        if ($this->product && $this->product->hasLoaded()) return $this->product->getSlug();
        return '';
    }
	
	public function view()
    {
        if ($this->hasNotEmpty('slug'))
        {
            return $this->item();
        }

        Ajde::app()->getDocument()->setTitle('Shop');

        $products = new ProductCollection();
        $products->orderBy('sort', Ajde_Query::ORDER_ASC);

        $this->getView()->assign('products', $products);

		return $this->render();
	}

    public function item()
    {
        // we want to display published nodes only
        if (!(UserModel::getLoggedIn() && UserModel::getLoggedIn()->isAdmin())) {
            Ajde::app()->getRequest()->set('filterPublished', true);
        }

        // get the current slug
        $slug = $this->getSlug();

        /* @var $product ProductModel */
        $product = new ProductModel();
        $product->loadBySlug($slug);
        $this->product = $product;

        if ($product->getPublished() === false) {
            Ajde_Dump::warn('Previewing unpublished product');
        }

        // check if we have a hit
        if (!$product->hasLoaded()) {
            Ajde::app()->getResponse()->redirectNotFound();
        }

        Ajde_Event::trigger($this, 'onAfterProductLoaded', array($product));

        // update cache
        Ajde_Cache::getInstance()->updateHash($product->hash());
        Ajde_Cache::getInstance()->addLastModified(strtotime($product->updated));

        // set title
        if (!Ajde::app()->getDocument()->hasNotEmpty('title')) {
            Ajde::app()->getDocument()->setTitle($product->getTitle());
        }
        // set summary
        if ($product->content) {
            Ajde::app()->getDocument()->setDescription(Ajde_Component_String::trim(strip_tags($product->content), '100'));
        }

        // set author
        $product->loadParent('user');
        /** @var UserModel $owner */
        $owner = $product->getUser();
        Ajde::app()->getDocument()->setAuthor($owner->getFullname());

        // featured image
        if ($image = $product->featuredImage()) {
            Ajde::app()->getDocument()->setFeaturedImage($image);
        }

        // pass node to view
        $this->setAction('item');
        $this->getView()->assign('product', $product);

        // render the template
        return $this->render();
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

        // Can we skip this step?
        if (!$transaction->hasLoaded() && !Config::get('shopOfferLogin') && $cart->hasItems()) {
            $this->redirect('shop/transaction:setup');
        }
		
		$this->getView()->assign('cart', $cart);
		$this->getView()->assign('user', $this->getLoggedInUser());
		$this->getView()->assign('transaction', $transaction);
		
		return $this->render();
	}
}
