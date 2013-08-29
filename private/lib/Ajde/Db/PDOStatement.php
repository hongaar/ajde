<?php
/**
 * @source http://www.coderholic.com/php-database-query-logging-with-pdo/
 * Modified for use with Ajde_Document_Processor_Html_Debugger
 */
/** 
* PDOStatement decorator that logs when a PDOStatement is 
* executed, and the time it took to run 
*/  
class Ajde_Db_PDOStatement extends PDOStatement {  
    
	/**
	 * @see http://www.php.net/manual/en/book.pdo.php#73568
	 */
	public $dbh;
	
    protected function __construct($dbh) {
        $this->dbh = $dbh;
    }
	
    /** 
    * When execute is called record the time it takes and 
    * then log the query 
    * @return PDO result set 
    */  
    public function execute($input_parameters = null) {
    	//$cache = Ajde_Db_Cache::getInstance();
		$log = array('query' => '[PS] ' . $this->queryString);
		$start = microtime(true);
		try {
		//if (!$cache->has($this->queryString . serialize($input_parameters))) {  
			$result = parent::execute($input_parameters);
			//$cache->set($this->queryString . serialize($input_parameters), $result);
		//	$log['cache'] = false;			
		//} else {
		//	$result = $cache->get($this->queryString . serialize($input_parameters));
		//	$log['cache'] = true;
		//}  
		} catch (Exception $e) {
			if (Config::get('debug') === true) {
				dump($this->queryString);
				throw $e;
			} else {
				Ajde_Exception_Log::logException($e);
				return false;
			}			
		}
        $time = microtime(true) - $start;  
		$log['time'] = round($time * 1000, 0);
        Ajde_Db_PDO::$log[] = $log;
        return $result;  
    }
	
	public static function getEmulatedSql($sql, $PDOValues) {
		// @see http://stackoverflow.com/questions/210564/pdo-prepared-statements/1376838#1376838
		$keys = array();
		$values = array();
		foreach ($PDOValues as $key => $value) {
			if (is_string($key)) {
				$keys[] = '/:'.$key.'/';
			} else {
				$keys[] = '/[?]/';
			}
			if (is_null($value)) {
				$values[] = "NULL";
			} elseif (is_numeric($value)) {
				$values[] = intval($value);
			} elseif ($value instanceof Ajde_Db_Function) {
				$values[] = (string) $value;
			} else {
				$values[] = '"'.$value .'"';
			}
		}
		$query = preg_replace($keys, $values, $sql, -1, $count);
		return $query;
	}
}  