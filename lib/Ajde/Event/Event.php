<?php

class Ajde_Event extends Ajde_Object_Static
{
	protected static $eventStack = array();

	/**
	 * Register a callback on an object event. Note that when an object instance
	 * is passed, only the classname is used. Thus, an Ajde_Event can not be used
	 * to trigger different callbacks for the same event on different object instances
	 * of the same class.
	 * 
	 * @param mixed $object Object instance or classname triggering the event
	 * @param string $event Event name
	 * @param mixed $callback Callback
	 * @return boolean true
	 */
	public static function register($object, $event, $callback)
	{
		self::$eventStack[self::className($object)][$event][] = $callback;
		return true;
	}

	public static function unregister($object, $event)
	{
		if (isset(self::$eventStack[self::className($object)][$event]))
		{
			unset(self::$eventStack[self::className($object)][$event]);
			return true;
		}
		return false;		
	}
	
	public static function has($object, $event, $callback = null)
	{
		if (isset($callback)) {
			return
				isset(self::$eventStack[self::className($object)][$event]) &&
				in_array($callback, self::$eventStack[self::className($object)][$event]);
		} else {
			return isset(self::$eventStack[self::className($object)][$event]);
		}
	}

	public static function trigger($object, $event, array $parameters = array())
	{
		foreach(self::$eventStack as $className => $eventStack)
		{
			if (self::className($object) == $className ||
					is_subclass_of(self::className($object), $className))
			{
				if (isset($eventStack[$event])) {
					foreach($eventStack[$event] as $eventCallback)
					{						
						$retval = null;
						$callback = null;
						if (is_callable($eventCallback))
						{
							$callback = $eventCallback;
						}
						elseif (is_string($eventCallback))
						{
							if (is_callable(array($object, $eventCallback))) {
								$callback = array($object, $eventCallback);
							}
						}
						if (isset($callback))
						{
							$trace = debug_backtrace();
							$current = array_shift($trace);
							$caller = array_shift($trace);

							if (isset($caller['object']))
							{
								// http://www.php.net/manual/en/function.call-user-func.php
								// Note: Note that the parameters for call_user_func() are not passed by reference.
								$parameterArray = array_merge(array(&$caller['object']), $parameters);
								$retval = call_user_func_array($callback, $parameterArray);
								if (isset($retval)) {
									// TODO: Aborts execution of cueue!
									return $retval;
								}
							}
							elseif (isset($caller['class']) && isset($caller['function']) && $caller['type'] === '::')
							{
								// triggered from static context
								// TODO: update exception 90015								
								$retval = call_user_func_array($callback, $parameters);
								if (isset($retval)) {
									// TODO: Aborts execution of cueue!
									return $retval;
								}
							}
							elseif ($callback instanceof Closure)
							{
								$retval = $callback();
								if (isset($retval)) {
									// TODO: Aborts execution of cueue!
									return $retval;
								}
							}
							else
							{
								throw new Ajde_Exception("Event context needed to
									fire, none detected", 90015);
							}
						}
						else
						{
							// TODO: right now never fires in Object_Magic objects
							// because of the __call magic function. Workaround
							// could be something like in_array("bar",get_class_methods($f1)
							// see: http://php.net/manual/en/function.method-exists.php
							throw new Ajde_Exception('Callback is not valid', 90016);
						}
					}
				}
			}
		}
	}

	protected static function className($object)
	{
		if (is_object($object))
		{
			return get_class($object);
		}
		elseif (is_string($object) && Ajde_Core_Autoloader::exists($object))
		{
			return $object;
		}
		throw new Ajde_Exception('No classname or object instance given, or classname is incorrect', 90012);
	}
}