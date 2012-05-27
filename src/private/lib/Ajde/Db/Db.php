<?php

class Ajde_Db extends Ajde_Object_Singleton
{
	protected $_adapter = null;
	protected $_tables = null;
	
	const FIELD_TYPE_NUMERIC	= 'numeric';
	const FIELD_TYPE_TEXT		= 'text';
	const FIELD_TYPE_ENUM		= 'enum';
	const FIELD_TYPE_DATE		= 'date';
	const FIELD_TYPE_SPATIAL	= 'spatial';
	
	
	/**
	 * @return Ajde_Db
	 */
	public static function getInstance()
	{
    	static $instance;
    	return $instance === null ? $instance = new self : $instance;
	}
		
	protected function __construct()
	{
		$adapterName = 'Ajde_Db_Adapter_' . ucfirst(Config::get('dbAdapter'));
		$dsn = Config::get('dbDsn');
		$user = Config::get('dbUser');
		$password = Config::get('dbPassword');
		$this->_adapter = new $adapterName($dsn, $user, $password);
	}
	
	/**
	 * @return Ajde_Db_Adapter_Abstract
	 */
	public function getAdapter()
	{
		return $this->_adapter;
	}
	
	/**
	 * @return Ajde_Db_PDO
	 */
	function getConnection()
	{
		return $this->_adapter->getConnection();
	}
	
	function getTable($tableName)
	{
		if (!isset($this->_tables[$tableName])) {
			$this->_tables[$tableName] = new Ajde_Db_Table($tableName);
		}
		return $this->_tables[$tableName];
	}
}