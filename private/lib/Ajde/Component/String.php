<?php 

class Ajde_Component_String extends Ajde_Component
{
	protected static $_allowedTags = '<table><tr><td><th><tfoot><tbody><thead><a><br><p><div><ul><li><b><h1><h2><h3><h4><h5><h6><strong><i><em><u><img><span><pre>';
	
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
	
	public static function escape($var)
	{
		if (is_array($var)) {
			array_walk($var, array("Ajde_Component_String", "rescape"));
			return $var;
		} else {
			return self::_escape($var);
		}
	}
	
	private static function rescape(&$var, $key)
	{
		if (is_array($var)) {
			array_walk($var, array("Ajde_Component_String", "rescape"));
		} else {
			$var = htmlspecialchars($var, ENT_QUOTES);
		}	
	}
	
	private static function _escape($var)
	{
		return htmlspecialchars($var, ENT_QUOTES);
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
	
	// http://www.php.net/manual/en/language.types.boolean.php#101180
	public static function toBoolean ($val, $trueValues = array('yes', 'y', 'true'), $forceLowercase = true)
	{
		if (is_string($val)) {
			return (in_array(($forceLowercase ? strtolower($val) : $val), $trueValues));
		} else {
			return (boolean) $val;
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

    public static function ltrim($str, $start, $delim = '...') {
        $pos = strpos($str, ' ', $start);
        return $delim . substr($str, $pos);
    }

    public static function highlight($str, $search, $trim = 100, $delim = '...') {
        $str = str_ireplace($search, '<span class="highlight">' . $search . '</span>', $str);
        if ($trim) {
            if (($first = strpos($str, '<span class="highlight">')) > ($trim / 2)) {
                $str = self::ltrim($str, $first - ($trim / 2));
            }
            $str = self::trim($str, $trim, $delim);
        }
        return $str;
    }
	
	/**
	 *  UrlLinker - facilitates turning plain text URLs into HTML links.
	 *
	 *  Author: Søren Løvborg
	 *
	 *  To the extent possible under law, Søren Løvborg has waived all copyright
	 *  and related or neighboring rights to UrlLinker.
	 *  http://creativecommons.org/publicdomain/zero/1.0/
	 */

	/**
	 *  Transforms plain text into valid HTML, escaping special characters and
	 *  turning URLs into links.
	 */
	public static function link($text)
	{		
	   /*
		*  Regular expression bits used by htmlEscapeAndLinkUrls() to match URLs.
		*/
	   $rexScheme    = 'https?://';
	   // $rexScheme    = "$rexScheme|ftp://"; // Uncomment this line to allow FTP addresses.
	   $rexDomain    = '(?:[-a-zA-Z0-9]{1,63}\.)+[a-zA-Z][-a-zA-Z0-9]{1,62}';
	   $rexIp        = '(?:[1-9][0-9]{0,2}\.|0\.){3}(?:[1-9][0-9]{0,2}|0)';
	   $rexPort      = '(:[0-9]{1,5})?';
	   $rexPath      = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
	   $rexQuery     = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
	   $rexFragment  = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
	   $rexUsername  = '[^]\\\\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64}';
	   $rexPassword  = $rexUsername; // allow the same characters as in the username
	   $rexUrl       = "($rexScheme)?(?:($rexUsername)(:$rexPassword)?@)?($rexDomain|$rexIp)($rexPort$rexPath$rexQuery$rexFragment)";
	   $rexTrailPunct= "[)'?.!,;:]"; // valid URL characters which are not part of the URL if they appear at the very end
	   $rexNonUrl    = "[^-_$+.!*'(),;/?:@=&a-zA-Z0-9]"; // characters that should never appear in a URL
	   $rexUrlLinker = "{\\b$rexUrl(?=$rexTrailPunct*($rexNonUrl|$))}";

	   /**
		*  $validTlds is an associative array mapping valid TLDs to the value true.
		*  Since the set of valid TLDs is not static, this array should be updated
		*  from time to time.
		*
		*  List source:  http://data.iana.org/TLD/tlds-alpha-by-domain.txt
		*  Last updated: 2012-09-06
		*/
		$validTlds = array_fill_keys(explode(" ", ".ac .ad .ae .aero .af .ag .ai .al .am .an .ao .aq .ar .arpa .as .asia .at .au .aw .ax .az .ba .bb .bd .be .bf .bg .bh .bi .biz .bj .bm .bn .bo .br .bs .bt .bv .bw .by .bz .ca .cat .cc .cd .cf .cg .ch .ci .ck .cl .cm .cn .co .com .coop .cr .cu .cv .cw .cx .cy .cz .de .dj .dk .dm .do .dz .ec .edu .ee .eg .er .es .et .eu .fi .fj .fk .fm .fo .fr .ga .gb .gd .ge .gf .gg .gh .gi .gl .gm .gn .gov .gp .gq .gr .gs .gt .gu .gw .gy .hk .hm .hn .hr .ht .hu .id .ie .il .im .in .info .int .io .iq .ir .is .it .je .jm .jo .jobs .jp .ke .kg .kh .ki .km .kn .kp .kr .kw .ky .kz .la .lb .lc .li .lk .lr .ls .lt .lu .lv .ly .ma .mc .md .me .mg .mh .mil .mk .ml .mm .mn .mo .mobi .mp .mq .mr .ms .mt .mu .museum .mv .mw .mx .my .mz .na .name .nc .ne .net .nf .ng .ni .nl .no .np .nr .nu .nz .om .org .pa .pe .pf .pg .ph .pk .pl .pm .pn .post .pr .pro .ps .pt .pw .py .qa .re .ro .rs .ru .rw .sa .sb .sc .sd .se .sg .sh .si .sj .sk .sl .sm .sn .so .sr .st .su .sv .sx .sy .sz .tc .td .tel .tf .tg .th .tj .tk .tl .tm .tn .to .tp .tr .travel .tt .tv .tw .tz .ua .ug .uk .us .uy .uz .va .vc .ve .vg .vi .vn .vu .wf .ws .xn--0zwm56d .xn--11b5bs3a9aj6g .xn--3e0b707e .xn--45brj9c .xn--80akhbyknj4f .xn--80ao21a .xn--90a3ac .xn--9t4b11yi5a .xn--clchc0ea0b2g2a9gcd .xn--deba0ad .xn--fiqs8s .xn--fiqz9s .xn--fpcrj9c3d .xn--fzc2c9e2c .xn--g6w251d .xn--gecrj9c .xn--h2brj9c .xn--hgbk6aj7f53bba .xn--hlcj6aya9esc7a .xn--j6w193g .xn--jxalpdlp .xn--kgbechtv .xn--kprw13d .xn--kpry57d .xn--lgbbat1ad8j .xn--mgb9awbf .xn--mgbaam7a8h .xn--mgbayh7gpa .xn--mgbbh1a71e .xn--mgbc0a9azcg .xn--mgberp4a5d4ar .xn--o3cw4h .xn--ogbpf8fl .xn--p1ai .xn--pgbs0dh .xn--s9brj9c .xn--wgbh1c .xn--wgbl6a .xn--xkc2al3hye2a .xn--xkc2dl3a5ee0h .xn--yfro4i67o .xn--ygbi2ammx .xn--zckzah .xxx .ye .yt .za .zm .zw"), true);
		
		$html = '';

		$position = 0;
		while (preg_match($rexUrlLinker, $text, $match, PREG_OFFSET_CAPTURE, $position))
		{
			list($url, $urlPosition) = $match[0];

			// Add the text leading up to the URL.
			$html .= substr($text, $position, $urlPosition - $position);

			$scheme      = $match[1][0];
			$username    = $match[2][0];
			$password    = $match[3][0];
			$domain      = $match[4][0];
			$afterDomain = $match[5][0]; // everything following the domain
			$port        = $match[6][0];
			$path        = $match[7][0];

			// Check that the TLD is valid or that $domain is an IP address.
			$tld = strtolower(strrchr($domain, '.'));
			if (preg_match('{^\.[0-9]{1,3}$}', $tld) || isset($validTlds[$tld]))
			{
				// Do not permit implicit scheme if a password is specified, as
				// this causes too many errors (e.g. "my email:foo@example.org").
				if (!$scheme && $password)
				{
					$html .= $username;

					// Continue text parsing at the ':' following the "username".
					$position = $urlPosition + strlen($username);
					continue;
				}

				if (!$scheme && $username && !$password && !$afterDomain)
				{
					// Looks like an email address.
					$completeUrl = "mailto:$url";
					$linkText = $url;
				}
				else
				{
					// Prepend http:// if no scheme is specified
					$completeUrl = $scheme ? $url : "http://$url";
					$linkText = "$domain$port$path";
				}

				$linkHtml = '<a href="' . htmlspecialchars($completeUrl) . '" target="_blank">'
					. $linkText
					. '</a>';

				// Cheap e-mail obfuscation to trick the dumbest mail harvesters.
				$linkHtml = str_replace('@', '&#64;', $linkHtml);

				// Add the hyperlink.
				$html .= $linkHtml;
			}
			else
			{
				// Not a valid URL.
				$html .= $url;
			}

			// Continue text parsing from after the URL.
			$position = $urlPosition + strlen($url);
		}

		// Add the remainder of the text.
		$html .= substr($text, $position);
		return $html;
	}
	
	public static function encrypt($text) {
		try {
			return trim(
				base64_encode(
					gzdeflate(
						mcrypt_encrypt(
							MCRYPT_RIJNDAEL_256,
							Config::get('secret'),
							$text,
							MCRYPT_MODE_ECB,
							mcrypt_create_iv(
								mcrypt_get_iv_size(
									MCRYPT_RIJNDAEL_256,
									MCRYPT_MODE_ECB
								),
								MCRYPT_RAND
							)
						),
						9
					)
				)
			);
		} catch (Exception $e) {
			return false;
		}
	}
	
	public static function decrypt($text) {
		if (empty($text)) {
			return false;
		}
		$oldErrRep = error_reporting(0);
		try {
			$decrypted = trim(
				mcrypt_decrypt(
					MCRYPT_RIJNDAEL_256,
					Config::get('secret'),
					gzinflate(base64_decode($text)),
					MCRYPT_MODE_ECB,
					mcrypt_create_iv(
						mcrypt_get_iv_size(
							MCRYPT_RIJNDAEL_256,
							MCRYPT_MODE_ECB
						),
						MCRYPT_RAND
					)
				)
			);
			error_reporting($oldErrRep);
			return $decrypted;
		} catch (Exception $e) {
			error_reporting($oldErrRep);
			return false;
		}
	}

    public static function textDiff($from, $to, $truncate = false)
    {
        require_once 'lib/finediff.php';
        $diff = new FineDiff($from, $to, FineDiff::$wordGranularity);
        if (!$truncate) {
            return $diff->renderDiffToHTML();
        } else {
            $html = $diff->renderDiffToHTML();

            // find first change
            $firstDel = strpos($html, "<del>");
            $firstIns = strpos($html, "<ins>");
            if ($firstDel === false) $firstDel = $firstIns;
            if ($firstIns === false) $firstIns = $firstDel;
            $first = min($firstDel, $firstIns);
            $first = max(0, $first - $truncate);

            // find last change
            $lastDel = strrpos($html, "</del>");
            $lastIns = strrpos($html, "</ins>");
            if ($lastDel === false) $lastDel = $lastIns;
            if ($lastIns === false) $lastIns = $lastDel;
            $last = max($lastDel, $lastIns);
            $last = min(strlen($html), $last + $truncate);

            // create truncated string
            return ($first > 0 ? '<span>....</span> ' : '') . substr($html, $first, $last - $first) . ($last < strlen($html)  ? ' <span>....</span>' : '');
        }
    }
	
	public static function time2str($date, $today = false)
	{
		if (!$today) {
			$today = new DateTime();			
			$today = $today->format('U');
		}
		$diff = $today - $date;
		if($diff == 0)
			return 'now';
		elseif($diff > 0)
		{
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
				if($diff < 60) return 'just now';
				if($diff < 120) return '1 minute ago';
				if($diff < 3600) return floor($diff / 60) . ' minutes ago';
				if($diff < 7200) return '1 hour ago';
				if($diff < 86400) return floor($diff / 3600) . ' hours ago';
			}
			if($day_diff == 1) return 'yesterday';
			if($day_diff < 7) return $day_diff . ' day' . ($day_diff != 1 ? 's' : '') . ' ago';
			if($day_diff < 31) return ceil($day_diff / 7) . ' week' . (ceil($day_diff / 7) != 1 ? 's' : '') . ' ago';
			if($day_diff < 60) return 'last month';
			return date('F Y', $date);
		}
		else
		{
			$diff = abs($diff);
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
				if($diff < 120) return 'in a minute';
				if($diff < 3600) return 'in ' . floor($diff / 60) . ' minutes';
				if($diff < 7200) return 'in an hour';
				if($diff < 86400) return 'in ' . floor($diff / 3600) . ' hours';
			}
			if($day_diff == 1) return 'tomorrow';
			if($day_diff < 4) return date('l', $date);
			if($day_diff < 7 + (7 - date('w'))) return 'next week';
			if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' week' . (ceil($day_diff / 7) != 1 ? 's' : '');
			if(date('n', $date) == date('n') + 1) return 'next month';
			return date('F Y', $date);
		}
	}

	public static function toBytes($str)
	{
		$val = trim($str);
		$last = strtolower($str[strlen($str) - 1]);
		switch ($last)
		{
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}
}