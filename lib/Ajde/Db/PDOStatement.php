<?php
/**
 * @source http://www.coderholic.com/php-database-query-logging-with-pdo/
 * Modified for use with Ajde_Document_Processor_Html_Debugger
 */

/**
 * PDOStatement decorator that logs when a PDOStatement is
 * executed, and the time it took to run.
 */
class Ajde_Db_PDOStatement extends PDOStatement
{
    /**
     * @see http://www.php.net/manual/en/book.pdo.php#73568
     */
    public $dbh;

    protected function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    /**
     * When execute is called record the time it takes and
     * then log the query.
     *
     * @param array $input_parameters
     *
     * @throws Ajde_Db_Exception
     * @throws Ajde_Exception
     *
     * @return PDO result set
     */
    public function execute($input_parameters = null)
    {
        $log = ['query' => ''];
        if (config('app.debug') === true) {
            //$cache = Ajde_Db_Cache::getInstance();
            if (count($input_parameters)) {
                $log = ['query' => vsprintf(str_replace('?', '%s', $this->queryString), $input_parameters)];
            } else {
                $log = ['query' => '[PS] '.$this->queryString];
            }
            // add backtrace
            $i = 0;
            $source = [];
            foreach (array_reverse(debug_backtrace()) as $item) {
                try {
                    $line = issetor($item['line']);
                    $file = issetor($item['file']);
                    $source[] = sprintf('%s. <em>%s</em>%s<strong>%s</strong> (%s on line %s)',
                        $i,
                        !empty($item['class']) ? $item['class'] : '&lt;unknown class&gt;',
                        // Assume of no classname is available, dumped from template.. (naive)
                        !empty($item['type']) ? $item['type'] : '::',
                        !empty($item['function']) ? $item['function'] : '&lt;unknown function&gt;',
                        $file,
                        $line);
                } catch (Exception $e) {
                }

                $i++;
            }
            $hash = md5(implode('', $source).microtime());

            $log['query'] = '<a href="javascript:void(0)" onclick="$(\'#'.$hash.'\').slideToggle(\'fast\');" style="color: black;">'.$log['query'].'</a>';
            $log['query'] .= '<div id="'.$hash.'" style="display: none;">'.implode('<br/>', $source).'</div>';
        }
        // start timer
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
            if (substr_count(strtolower($e->getMessage()), 'integrity constraint violation')) {
                throw new Ajde_Db_IntegrityException($e->getMessage());
            } else {
                if (config('app.debug') === true) {
                    if (isset($this->queryString)) {
                        dump($this->queryString);
                    }
                    dump('Go to '.config('app.rootUrl').'?install=1 to install DB');
                    throw new Ajde_Db_Exception($e->getMessage());
                } else {
                    Ajde_Exception_Log::logException($e);
                    die('DB connection problem. <a href="?install=1">Install database?</a>');
                }
            }
        }
        $time = microtime(true) - $start;
        $log['time'] = round($time * 1000, 0);
        Ajde_Db_PDO::$log[] = $log;

        return $result;
    }

    public static function getEmulatedSql($sql, $PDOValues)
    {
        // @see http://stackoverflow.com/questions/210564/pdo-prepared-statements/1376838#1376838
        $keys = [];
        $values = [];
        foreach ($PDOValues as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            } else {
                $keys[] = '/[?]/';
            }
            if (is_null($value)) {
                $values[] = 'NULL';
            } elseif (is_numeric($value)) {
                $values[] = intval($value);
            } elseif ($value instanceof Ajde_Db_Function) {
                $values[] = (string) $value;
            } else {
                $values[] = '"'.$value.'"';
            }
        }
        $query = preg_replace($keys, $values, $sql, -1, $count);

        return $query;
    }
}
