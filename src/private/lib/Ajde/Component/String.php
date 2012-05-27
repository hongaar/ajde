<?php 

class Ajde_Component_String extends Ajde_Component
{
	protected static $_allowedTags = '<table><tr><td><th><tfoot><tbody><thead><a><br><p><div><ul><li><b><h1><h2><h3><h4><h5><h6><strong><i><em><u><img>';
	
	public static function processStatic(Ajde_Template_Parser $parser, $attributes)
	{
		$instance = new self($parser, $attributes);
		return $instance->process();
	}
	
	protected function _init()
	{
		return array(
			'escape' 	=> 'escape',
			'clean' 	=> 'clean'
		);
	}
	
	public function process()
	{
		switch($this->_attributeParse()) {
			case 'escape':
				$var = $this->attributes['var'];
				return $this->escape($var);
				break;	
			case 'clean':
				$var = $this->attributes['var'];
				return $this->clean($var);
				break;				
		}		
		// TODO:
		throw new Ajde_Component_Exception();	
	}
	
	public static function escape(&$var, $key = null)
	{
		if (isset($key)) {
			// called from array_walk
			if (is_array($var)) {
				array_walk($var, array("Ajde_Component_String", "escape"));
			} else {
				$var = htmlspecialchars($var, ENT_QUOTES);
			}			
		} else {
			return htmlspecialchars($var, ENT_QUOTES);
		}
	}
	
	public static function clean($var)
	{		
		$clean = strip_tags($var, self::$_allowedTags);
		return $clean;
	}
	
	public static function purify($var)
	{
		$external = Ajde_Core_ExternalLibs::getInstance();
		if ($external->has("HTMLPurifier")) {
			$purifier = $external->get("HTMLPurifier");
			
			/* @var $purifier HTMLPurifier */
			
			$config = HTMLPurifier_Config::createDefault();
			
			$config->set('AutoFormat.AutoParagraph', true);
			$config->set('AutoFormat.DisplayLinkURI', false);
			$config->set('AutoFormat.Linkify', false);
			$config->set('AutoFormat.RemoveEmpty', true);
			$config->set('AutoFormat.RemoveSpansWithoutAttributes', true);
			
			$config->set('CSS.AllowedProperties', '');
			
			$config->set('HTML.Doctype', 'XHTML 1.0 Strict');
			
			$config->set('URI.DisableExternalResources', true);
			
			$purifier->config = HTMLPurifier_Config::create($config);
			
			return $purifier->purify($var);
		} else {
			return self::clean($var);
		}
	}
	
	public static function makePlural($count, $singular)
	{
		$count = (int) $count;
		$ret = $count . ' ' . $singular;
		if ($count > 1 || $count == 0) {
			$ret .= 's';
		}
		return $ret;
	}
	
	/**
	 * Validate an email address.
	 * Provide email address (raw input)
	 * Returns true if the email address has the email 
	 * address format and the domain exists.
	 * 
	 * @see http://www.linuxjournal.com/article/9585?page=0,3
	 * @return bool
	 */
	public static function validEmail($email)
	{
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex)
		{
			$isValid = false;
		}
		else
		{
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64)
			{
				// local part length exceeded
				$isValid = false;
			}
			else if ($domainLen < 1 || $domainLen > 255)
			{
				// domain part length exceeded
				$isValid = false;
			}
			else if ($local[0] == '.' || $local[$localLen-1] == '.')
			{
				// local part starts or ends with '.'
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $local))
			{
				// local part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
			{
				// character not valid in domain part
				$isValid = false;
			}
			else if (preg_match('/\\.\\./', $domain))
			{
				// domain part has two consecutive dots
				$isValid = false;
			}
			else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
						str_replace("\\\\","",$local)))
			{
				// character not valid in local part unless 
				// local part is quoted
				if (!preg_match('/^"(\\\\"|[^"])+"$/',
					str_replace("\\\\","",$local)))
				{
					$isValid = false;
				}
			}
			if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
			{
				// domain not found in DNS
				$isValid = false;
			}
		}
		return $isValid;
	}
	
	/**
	* Cut string to n symbols and add delim but do not break words.
	*
	* Example:
	* <code>
	*  $string = 'this sentence is way too long';
	*  echo neat_trim($string, 16);
	* </code>
	*
	* Output: 'this sentence is...'
	*
	* @access public
	* @param string string we are operating with
	* @param integer character count to cut to
	* @param string|NULL delimiter. Default: '...'
	* @return string processed string
	 * 
	 * @see http://www.justin-cook.com/wp/2006/06/27/php-trim-a-string-without-cutting-any-words/
	**/
	public static function trim($str, $n, $delim = '...') {
		$len = strlen($str);
		if ($len > $n) {
			$str = str_replace("\n","",$str);
			$str = str_replace("\r","",$str);
			$n = $n - strlen($delim);
			preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
			return rtrim($matches[1]) . $delim;
		} else {
			return $str;
		}
	}
}