<?php

class Ajde_Dump extends Ajde_Object_Static
{
    public static $dump = [];
    public static $warn = [];

    public static function dump($var, $expand = true)
    {
        $i = 0;
        $line = null;
        foreach (debug_backtrace() as $item) {
            try {
                $source = sprintf("%s. dumped from <em>%s</em>%s<strong>%s</strong> (line %s)",
                    count(self::$dump) + 1,
                    isset($item['class']) ? (is_object($item['class']) ? get_class($item['class']) : $item['class']) : '&lt;unknown class&gt; (in <span style=\'font-size: 0.8em;\'>' . print_r($item['args'][0]) . '</span>)',
                    // Assume of no classname is available, dumped from template.. (naive)
                    !empty($item['type']) ? $item['type'] : '::',
                    !empty($item['function']) ? $item['function'] : '&lt;unknown function&gt;',
                    $line);
                $line = issetor($item['line'], null);
            } catch (Exception $e) {
            }

            if ($i == 2) {
                break;
            }
            $i++;
        }
        self::$dump[$source] = [$var, $expand];
    }

    public static function warn($message)
    {
        self::$warn[] = $message;
    }

    public static function getAll()
    {
        return self::$dump;
    }

    public static function getWarnings()
    {
        return self::$warn;
    }
}
