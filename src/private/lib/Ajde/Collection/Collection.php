<?php

class Ajde_Collection extends Ajde_Object_Standard implements Iterator, Countable {
	
	/**
	 * @var string
	 */
	protected $_modelName;
	
	/**
	 * @var PDO
	 */
	protected $_connection;
	
	/**
	 * @var PDOStatement
	 */
	protected $_statement;
	
	/**
	 * @var Ajde_Query
	 */
	protected $_query;
	
	protected $_link = array();
	
	/**
	 * @var Ajde_Db_Table
	 */
	protected $_table;
	
	protected $_filters = array();	
	public $_filterValues = array();
	
	/**
	 * @var Ajde_Collection_View
	 */
	protected $_view;
	
	// For Iterator
	protected $_items = null;
	protected $_position = 0;
	
	private $_sqlInitialized = false;
	private $_queryCount;
	
	public static function register($controller)
	{
		// Extend Ajde_Controller
		if (!Ajde_Event::has('Ajde_Controller', 'call', 'Ajde_Collection::extendController')) {
			Ajde_Event::register('Ajde_Controller', 'call', 'Ajde_Collection::extendController');
		}
		// Extend autoloader
		if ($controller instanceof Ajde_Controller) {
			Ajde_Core_Autoloader::addDir(MODULE_DIR.$controller->getModule().'/model/');
		} elseif ($controller === '*') {
			self::registerAll();
		} else {
			Ajde_Core_Autoloader::addDir(MODULE_DIR.$controller.'/model/');
		}		
	}
	
	public static function extendController(Ajde_Controller $controller, $method, $arguments)
	{
		// Register getCollection($name) function on Ajde_Controller
		if ($method === 'getCollection') {
			return self::getCollection($arguments[0]);
		}
		// TODO: if last triggered in event cueue, throw exception
		// throw new Ajde_Exception("Call to undefined method ".get_class($controller)."::$method()", 90006);
		// Now, we give other callbacks in event cueue chance to return
		return null; 
	}
	
	public static function getCollection($name)
	{
		$collectionName = ucfirst($name) . 'Collection';
		return new $collectionName();
	}
	
	public function __construct()
	{
		$this->_modelName = str_replace('Collection', '', get_class($this)) . 'Model';		
		$this->_connection = Ajde_Db::getInstance()->getConnection();
		
		$tableNameCC = str_replace('Collection', '', get_class($this));
		$tableName = $this->fromCamelCase($tableNameCC);
		
		$this->_table = Ajde_Db::getInstance()->getTable($tableName);
		$this->_query = new Ajde_Query();
	}
	
	public function reset()
	{
		parent::reset();
		$this->_query = new Ajde_Query();
		$this->_filters = array();	
		$this->_filterValues = array();
		$this->_sqlInitialized = false;
	}
	
	public function __sleep()
	{
		return array('_modelName', '_items');
	}

	public function __wakeup()
	{
	}
	
	public function rewind() {
		if (!isset($this->_items)) {
    		$this->load();
    	}
        $this->_position = 0;
    }

    public function current() {    	
        return $this->_items[$this->_position];
    }

    public function key() {
        return $this->_position;
    }

    public function next() {
        $this->_position++;
    }
	
	public function count($query = false) 
	{
		if ($query == true) {
			if (!isset($this->_queryCount)) {				
				$this->_statement = $this->getConnection()->prepare($this->getCountSql());
				foreach($this->getFilterValues() as $key => $value) {
					if (is_null($value)) {
						$this->_statement->bindValue(":$key", null, PDO::PARAM_NULL);
					} else {
						$this->_statement->bindValue(":$key", $value, PDO::PARAM_STR);
					}
				}
				$this->_statement->execute();
				$result = $this->_statement->fetch(PDO::FETCH_ASSOC);
				$this->_queryCount = $result['count'];
			}
			return $this->_queryCount;
		} else {
			if (!isset($this->_items)) {
				$this->load();
			}
			return count($this->_items);
		}
	}
	
	public function find($field, $value) {
		foreach($this as $item) {
			if ($item->{$field} == $value) {
				return $item;
			}
		}
		return false;
	}

    function valid() {
        return isset($this->_items[$this->_position]);
    }
	
	/**
	 * @return Ajde_Db_PDO
	 */
	public function getConnection()
	{
		return $this->_connection;
	}
	
	/**
	 * @return Ajde_Db_Table
	 */
	public function getTable()
	{
		return $this->_table;
	}
	
	/**
	 * @return PDOStatement
	 */
	public function getStatement()
	{
		return $this->_statement;
	}
	
	/**
	 * @return Ajde_Query
	 */
	public function getQuery()
	{
		return $this->_query;
	}
		
	public function populate($array)
	{
		$this->reset();
		$this->_data = $array;
	}
	
	public function getLink($modelName, $value)
	{
		if (!array_key_exists($modelName, $this->_link)) {
			// TODO:
			throw new Ajde_Exception('Link not defined...');
		}
		return new Ajde_Filter_Link($this, $modelName, $this->_link[$modelName], $value);
	}
	
	// Chainable collection methods
	
	public function addFilter(Ajde_Filter $filter)
	{
		$this->_filters[] = $filter;
		return $this;		
	}
	
	public function orderBy($field, $direction = Ajde_Query::ORDER_ASC)
	{
		$this->getQuery()->addOrderBy($field, $direction);
		return $this;
	}
	
	public function limit($count, $start = 0)
	{
		$this->getQuery()->limit((int) $count, (int) $start);
		return $this;
	}
	
	public function filter($field, $value, $comparison = Ajde_Filter::FILTER_EQUALS, $operator = Ajde_Query::OP_AND)
	{
		$this->addFilter(new Ajde_Filter_Where($field, $comparison, $value, $operator));
		return $this;
	}
	
	// View functions
	
	public function setView(Ajde_Collection_View $view)
	{
		$this->_view = $view;
	}
		
	/**
	 * @return Ajde_Collection_View
	 */
	public function getView()
	{
		return $this->_view;
	}
	
	/**
	 * @return boolean
	 */
	public function hasView()
	{
		return isset($this->_view) && $this->_view instanceof Ajde_Collection_View;
	}
	
	
	public function applyView(Ajde_Collection_View $view = null)
	{
		if (!$this->hasView() && !isset($view)) {
			// TODO:
			throw new Ajde_Exception();
		}
		
		if (isset($view)) {
			$this->setView($view);
		} else {
			$view = $this->getView();
		}
		
		// LIMIT
		$this->limit($view->getPageSize(), $view->getRowStart());
		
		// ORDER BY
		if (!$view->isEmpty('orderBy')) {
			$oldOrderBy = $this->getQuery()->orderBy;
			$this->getQuery()->orderBy = array();
			$this->orderBy($view->getOrderBy(), $view->getOrderDir());
			foreach($oldOrderBy as $orderBy) {
				$this->orderBy($orderBy['field'], $orderBy['direction']);
			}
		}
		
		// FILTER
		if (!$view->isEmpty('filter')) {
			foreach($view->getFilter() as $fieldName => $filterValue) {
				if (!empty($filterValue)) {
					$this->addFilter(new Ajde_Filter_Where($fieldName, Ajde_Filter::FILTER_EQUALS, $filterValue));
				}
			}
		}
		
		// SEARCH
		if (!$view->isEmpty('search')) {
			$this->addTextFilter($view->getSearch());
		}
	}
	
	public function addTextFilter($text)
	{
		$searchFilter = new Ajde_Filter_WhereGroup;
		$fieldOptions = $this->getTable()->getFieldProperties();			
		foreach($fieldOptions as $fieldName => $fieldProperties) {
			switch ($fieldProperties['type']) {
				case Ajde_Db::FIELD_TYPE_TEXT:						
				case Ajde_Db::FIELD_TYPE_ENUM:
					$searchFilter->addFilter(new Ajde_Filter_Where($fieldName, Ajde_Filter::FILTER_LIKE, '%' . $text . '%', Ajde_Query::OP_OR));						
					break;					
				default:
					break;
			}
		}
		$this->addFilter($searchFilter);		
	}
	
	public function getSql()
	{
		if (!$this->_sqlInitialized) {
			foreach($this->getTable()->getFieldNames() as $field) {
				$this->getQuery()->addSelect((string) $this->getTable() . '.' . $field);
			}
			if (!empty($this->_filters)) {
				foreach($this->getFilter('select') as $select) {
					call_user_func_array(array($this->getQuery(), 'addSelect'), $select);
				}
			}
			$this->getQuery()->addFrom($this->_table);
			if (!empty($this->_filters)) {
				foreach($this->getFilter('join') as $join) {
					call_user_func_array(array($this->getQuery(), 'addJoin'), $join);
				}
			}
			if (!empty($this->_filters)) {
				foreach($this->getFilter('where') as $where) {
					call_user_func_array(array($this->getQuery(), 'addWhere'), $where);
				}
			}
		}
		$this->_sqlInitialized = true;
		return $this->getQuery()->getSql();
	}
	
	public function getCountSql()
	{
		// Make sure to load the filters
		$this->getSql();
		$query = clone $this->getQuery();
		/* @var $query Ajde_Query */
		$query->select = array();
		$query->limit = array('start' => null, 'count' => null);	
		$query->addSelect('COUNT(*) AS count');
		return $query->getSql();
	}
	
	public function getEmulatedSql()
	{
		return Ajde_Db_PDOStatement::getEmulatedSql($this->getSql(), $this->getFilterValues());
	}
	
	public function getFilter($queryPart)
	{
		$arguments = array();
		foreach($this->_filters as $filter) {
			$prepared = $filter->prepare($this->getTable());			
			if (isset($prepared[$queryPart])) {
				if (isset($prepared[$queryPart]['values'])) {
					$this->_filterValues = array_merge($this->_filterValues, $prepared[$queryPart]['values']);
				}
				$arguments[] = $prepared[$queryPart]['arguments'];
			}			
		}
		if (empty($arguments)) {
		 	return array();
		} else {
			return $arguments; 
		}
	}	
	
	public function getFilterValues()
	{
		return $this->_filterValues;
	}
	
	// Load the collection
	public function load()
	{
		if (!$this->getConnection() instanceof Ajde_Db_PDO) {
			// return false;
		}
		$this->_statement = $this->getConnection()->prepare($this->getSql());
		foreach($this->getFilterValues() as $key => $value) {
			if (is_null($value)) {
				$this->_statement->bindValue(":$key", null, PDO::PARAM_NULL);
			} else {
				$this->_statement->bindValue(":$key", $value, PDO::PARAM_STR);
			}
		}
		$this->_statement->execute();
		return $this->_items = $this->_statement->fetchAll(PDO::FETCH_CLASS, $this->_modelName);
	}
	
	public function loadParents()
	{
		if (count($this) > 0) {
			foreach($this as $model) {
				$model->loadParents();
			}
		}
	}
	
	public function length()
	{
		if (!isset($this->_items)) {
			$this->load();
		}
		return count($this->_items);
	}
	
	public function hash()
	{
		$str = '';
		foreach($this as $item) {
			$str .= implode($item->values());
		}
		return md5($str);
	}
}