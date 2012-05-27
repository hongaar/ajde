<?php

class Ajde_Dump extends Ajde_Object_Static
{
	public static $dump = array();
	public static $warn = array();
	
	public static function dump($var, $collapse = false) {
		$i = 0;
		$line = null;
		foreach(debug_backtrace() as $item) {
			$source = sprintf("%s. dumped from <em>%s</em>%s<strong>%s</strong> (line %s)",
				count(self::$dump) + 1,							
				!empty($item['class']) ? $item['class'] : '&lt;unknown class&gt; (in <span style=\'font-size: 0.8em;\'>' . $item['args'][0] . '</span>)', // Assume of no classname is available, dumped from template.. (naive)
				!empty($item['type']) ? $item['type'] : '::',
				!empty($item['function']) ? $item['function'] : '&lt;unknown function&gt;',
				$line);
			$line = issetor($item['line'], null);	
			if ($i == 2) { break; }
			$i++;
		}
		self::$dump[$source] = array($var, $collapse);
	}
	
	public static function warn($message) {
		self::$warn[] = $message;
	}
	
	public static function getAll() {
		return self::$dump;
	}
	
	public static function getWarnings() {
		return self::$warn;
	}
}
