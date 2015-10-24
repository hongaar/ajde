<?php

class ProductModel extends Ajde_Model
{
	protected $_autoloadParents = true;
	protected $_displayField = 'title';

    protected $_slugPrefix = 'shop';

    public static $imageDir = 'upload/shop/';
	
	public function __construct() {
		Ajde_Event::register($this, 'afterCrudLoaded', array($this, 'parseForCrud'));
		parent::__construct();
	}

    /**
     *
     * @param int $id
     * @return ProductModel|boolean
     */
    public static function fromPk($id)
    {
        $product = new self();
        if ($product->loadByPK($id)) {
            return $product;
        }
        return false;
    }

    /**
     *
     * @param $slug
     * @return bool|ProductModel
     */
    public static function fromSlug($slug)
    {
        $product = new self();
        if ($product->loadBySlug($slug)) {
            return $product;
        }
        return false;
    }

    public function beforeSave()
    {
        // filter slug
        $this->slug = $this->_sluggify($this->slug);

        if (empty($this->slug)) {
            $this->slug = new Ajde_Db_Function('slug');
        }
    }

    public function beforeInsert()
    {
        // Slug
        $this->slug = $this->_makeSlug();
    }

    public function parseForCrud(Ajde_Crud $crud)
    {
        // Do something
    }

    /**
     * PRICE
     */

	public function getVATPercentage()
	{
		if ($this->hasVat() && !$this->getVat() instanceof Ajde_Model) {
			$this->loadParents();
		}
		return $this->hasVat() ? ((float) $this->getVat()->getPercentage() / 100) : 0;
	}

    public function getFormattedPriceInclVat()
    {
        return money_format('%!i', $this->getUnitprice() * (1 + $this->getVATPercentage()));
    }

    public function getFormattedPriceExclVat()
    {
        return money_format('%!i', $this->getUnitprice());
    }

    public function getFormattedVat()
    {
        return money_format('%!i', $this->getUnitprice() * $this->getVATPercentage());
    }


    /**
     * IMAGE
     */

    /**
     * @return Ajde_Resource_Image|bool
     * @throws Ajde_Exception
     */
    public function getImage()
    {
        if ($this->hasNotEmpty('image'))
        {
            return new Ajde_Resource_Image(MEDIA_DIR . self::$imageDir . $this->get('image'));
        }

        return false;
    }

    public function featuredImage($width = 800)
    {
        if ($image = $this->getImage())
        {
            return $image->getUrl($width);
        }

        return false;
    }

    /**
     * SLUG
     */

    public function getSlug()
    {
        if (!$this->hasSlug()) {
            $this->slug = $this->_makeSlug();
        }
        return $this->_slugPrefix . '/' . $this->slug;
    }

    private function _makeSlug()
    {
        $name = $this->has('title') ? $this->title : '';

        $ghost = new self();
        $uniqifier = 0;

        do {
            $ghost->reset();
            $slug = $this->_sluggify($name);
            $slug = $slug . ($uniqifier > 0 ? '-' . $uniqifier : '');
            $ghost->loadBySlug($slug);
            $uniqifier++;
            if ($uniqifier >= 100) {
                throw new Ajde_Controller_Exception('Max recursion depth reached for setting slug');
            }
        } while($ghost->hasLoaded());

        return $slug;
    }

    private function _sluggify($name)
    {
        // @see http://stackoverflow.com/a/5240834
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        $slug = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $name);
        $slug = strtolower(trim($slug, '-'));
        $slug = preg_replace("/[\/_| -]+/", '-', $slug);
        return $slug;
    }

    /**
     * LOAD
     */

    public function loadBySlug($slug, $publishedCheck = false)
    {
        $this->loadByField('slug', $slug);
        if ($publishedCheck) {
            $this->filterPublished();
        }
        return $this->hasLoaded();
    }

    public function filterPublished()
    {
        if (false === $this->getPublished()) {
            $this->reset();
        }
    }

    protected function _load($sql, $values, $populate = true)
    {
        $return = parent::_load($sql, $values, $populate);
        if ($return && Ajde::app()->hasRequest() && Ajde::app()->getRequest()->getParam('filterPublished', false) ==  true) {
            $this->filterPublished();
        }
        return $return;
    }
}
