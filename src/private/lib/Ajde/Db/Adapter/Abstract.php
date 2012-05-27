<?php

abstract class Ajde_Db_Adapter_Abstract
{	
	public function __construct($dsn, $user, $password, $options)
	{
		$options = $options + array(
			// Not compatible with custom PDO::ATTR_STATEMENT_CLASS 
		    //PDO::ATTR_PERSISTENT 			=> true,					// Fast, please
		    PDO::ATTR_ERRMODE				=> PDO::ERRMODE_EXCEPTION 	// Exceptions, please);
		);
		try {
			$connection = new Ajde_Db_PDO($dsn, $user, $password, $options);
		} catch (Exception $e) {
			// Disable trace on this exception to prevent exposure of sensitive data
			// TODO: exception
			Ajde_Exception_Log::logException($e);
			throw new Ajde_Exception('Could not connect to database', 0, false);
		}
		$this->_connection = $connection;
	} 
	
	abstract public function getConnection();
	abstract public function getTableStructure($tableName);
	abstract public function getForeignKey($childTable, $parentTable);
	abstract public function getParents($childTable);
}