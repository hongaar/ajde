<?php

class Ajde_Core_Bootstrap
{	
	public function run()
	{
		$cue = Config::getInstance()->bootstrap;
		$this->runCue($cue);
	}

	public function runCue($cue) {
		/*
		 * Our bootstrapper calls the __bootstrap() methods on all modules defined
		 * in Config::get("bootstrap").
		 */
		$bootstrapFunction  = '__bootstrap';
		
		foreach($cue as $className)
		{
			// See if $className is a subclass of Ajde_Object
			if (!method_exists($className, "__getPattern")) {
				throw new Ajde_Exception("Class $className has no pattern
						defined while it is configured for bootstrapping", 90001);
			}
			// Get bootstrap function callback
			$mode = call_user_func(array($className,"__getPattern"));
			if ($mode === Ajde_Object::OBJECT_PATTERN_STANDARD)
			{
				$instance = new $className;
				$function = array($instance, $bootstrapFunction);
			}
			elseif ($mode === Ajde_Object::OBJECT_PATTERN_SINGLETON)
			{
				$instance = call_user_func("$className::getInstance");
				$function = array($instance, $bootstrapFunction);
			}
			elseif ($mode === Ajde_Object::OBJECT_PATTERN_STATIC)
			{
				$function = "$className::$bootstrapFunction";
			}
			elseif ($mode === null || $mode === Ajde_Object::OBJECT_PATTERN_UNDEFINED)
			{
				throw new Ajde_Exception("Class $className has no pattern
						defined while it is configured for bootstrapping", 90001);
			}
			// Execute bootstrap() function on $className
			if (!method_exists($className, $bootstrapFunction))
			{
				throw new Ajde_Exception("Bootstrap method in
						$className doesn't exist", 90002);
			}
			elseif (!call_user_func($function))
			{
				throw new Ajde_Exception("Bootstrap method in
						$className returned FALSE", 90003);
			}
		}
	}
}