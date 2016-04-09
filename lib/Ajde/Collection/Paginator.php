<?php

class Ajde_Collection_Paginator extends Ajde_Object_Standard implements Iterator, Countable
{
    protected $_items     = null;
    protected $_pageItems = null;

    protected $_pagesize = 10;
    protected $_page     = 1;

    protected $_position = 0;

    public function __construct($data, $pagesize = 10)
    {
        $this->_items    = $data;
        $this->_pagesize = $pagesize;
        $this->initPage();
    }

    public function reset()
    {
        parent::reset();
        $this->_items    = null;
        $this->_position = 0;
    }

    // Paginator
    public function setPagesize($pagesize)
    {
        $this->_pagesize = $pagesize;
        $this->initPage();
    }

    public function setPage($page)
    {
        $this->_page = (int)$page;
        $this->initPage();
    }

    public function getPagecount()
    {
        return ceil(count($this->_items) / $this->_pagesize);
    }

    private function initPage()
    {
        $this->_pageItems = array_slice($this->_items, ($this->_page - 1) * $this->_pagesize, $this->_pagesize, true);
    }

    // Iterator
    function rewind()
    {
        reset($this->_pageItems);
    }

    function current()
    {
        return current($this->_pageItems);
    }

    function key()
    {
        return key($this->_pageItems);
    }

    function next()
    {
        next($this->_pageItems);
    }

    function valid()
    {
        return key($this->_pageItems) !== null;
    }

    // Countable
    public function count()
    {
        return count($this->_pageItems);
    }
}
