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
        return [
            'escape' => 'escape',
            'clean'  => 'clean',
        ];
    }

    public function process()
    {
        switch ($this->_attributeParse()) {
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
            array_walk($var, ['Ajde_Component_String', 'rescape']);

            return $var;
        } else {
            return self::_escape($var);
        }
    }

    private static function rescape(&$var, $key)
    {
        if (is_array($var)) {
            array_walk($var, ['Ajde_Component_String', 'rescape']);
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
        if ($external->has('HTMLPurifier')) {
            $purifier = $external->get('HTMLPurifier');

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
    public static function toBoolean($val, $trueValues = ['yes', 'y', 'true'], $forceLowercase = true)
    {
        if (is_string($val)) {
            return in_array(($forceLowercase ? strtolower($val) : $val), $trueValues);
        } else {
            return (bool) $val;
        }
    }

    public static function makePlural($count, $singular)
    {
        $count = (int) $count;
        $ret = $count.' '.$singular;
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
     *
     * @return bool
     */
    public static function validEmail($email)
    {
        $isValid = true;
        $atIndex = strrpos($email, '@');
        if (is_bool($atIndex) && !$atIndex) {
            $isValid = false;
        } else {
            $domain = substr($email, $atIndex + 1);
            $local = substr($email, 0, $atIndex);
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } else {
                if ($domainLen < 1 || $domainLen > 255) {
                    // domain part length exceeded
                    $isValid = false;
                } else {
                    if ($local[0] == '.' || $local[$localLen - 1] == '.') {
                        // local part starts or ends with '.'
                        $isValid = false;
                    } else {
                        if (preg_match('/\\.\\./', $local)) {
                            // local part has two consecutive dots
                            $isValid = false;
                        } else {
                            if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
                                // character not valid in domain part
                                $isValid = false;
                            } else {
                                if (preg_match('/\\.\\./', $domain)) {
                                    // domain part has two consecutive dots
                                    $isValid = false;
                                } else {
                                    if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
                                        str_replace('\\\\', '', $local))
                                    ) {
                                        // character not valid in local part unless
                                        // local part is quoted
                                        if (!preg_match('/^"(\\\\"|[^"])+"$/',
                                            str_replace('\\\\', '', $local))
                                        ) {
                                            $isValid = false;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            if ($isValid && !(checkdnsrr($domain, 'MX') || checkdnsrr($domain, 'A'))) {
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
     * @param             string    string we are operating with
     * @param             int   character count to cut to
     * @param string|null delimiter . Default: '...'
     *
     * @return string processed string
     *
     * @see    http://www.justin-cook.com/wp/2006/06/27/php-trim-a-string-without-cutting-any-words/
     **/
    public static function trim($str, $n, $delim = '...')
    {
        $len = strlen($str);
        if ($len > $n) {
            $str = str_replace("\n", '', $str);
            $str = str_replace("\r", '', $str);
            $n = $n - strlen($delim);
            preg_match('/(.{'.$n.'}.*?)\b/', $str, $matches);

            return rtrim($matches[1]).$delim;
        } else {
            return $str;
        }
    }

    public static function ltrim($str, $start, $delim = '...')
    {
        $pos = strpos($str, ' ', $start);

        return $delim.substr($str, $pos);
    }

    public static function highlight($str, $search, $trim = 100, $delim = '...')
    {
        $str = str_ireplace($search, '<span class="highlight">'.$search.'</span>', $str);
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
        $rexScheme = 'https?://';
        // $rexScheme    = "$rexScheme|ftp://"; // Uncomment this line to allow FTP addresses.
        $rexDomain = '(?:[-a-zA-Z0-9]{1,63}\.)+[a-zA-Z][-a-zA-Z0-9]{1,62}';
        $rexIp = '(?:[1-9][0-9]{0,2}\.|0\.){3}(?:[1-9][0-9]{0,2}|0)';
        $rexPort = '(:[0-9]{1,5})?';
        $rexPath = '(/[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]*?)?';
        $rexQuery = '(\?[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
        $rexFragment = '(#[!$-/0-9:;=@_\':;!a-zA-Z\x7f-\xff]+?)?';
        $rexUsername = '[^]\\\\\x00-\x20\"(),:-<>[\x7f-\xff]{1,64}';
        $rexPassword = $rexUsername; // allow the same characters as in the username
        $rexUrl = "($rexScheme)?(?:($rexUsername)(:$rexPassword)?@)?($rexDomain|$rexIp)($rexPort$rexPath$rexQuery$rexFragment)";
        $rexTrailPunct = "[)'?.!,;:]"; // valid URL characters which are not part of the URL if they appear at the very end
        $rexNonUrl = "[^-_$+.!*'(),;/?:@=&a-zA-Z0-9]"; // characters that should never appear in a URL
        $rexUrlLinker = "{\\b$rexUrl(?=$rexTrailPunct*($rexNonUrl|$))}";

        /**
         *  $validTlds is an associative array mapping valid TLDs to the value true.
         *  Since the set of valid TLDs is not static, this array should be updated
         *  from time to time.
         *
         *  List source:  http://data.iana.org/TLD/tlds-alpha-by-domain.txt
         *  Version 2015062200, last updated mon jun 22 07:07:02 2015 utc
         */
        $validTlds = array_fill_keys(explode(' ',
            '.abb .abbott .abogado .ac .academy .accenture .accountant .accountants .active .actor .ad .ads .adult .ae .aeg .aero .af .afl .ag .agency .ai .aig .airforce .al .allfinanz .alsace .am .amsterdam .an .android .ao .apartments .aq .aquarelle .ar .archi .army .arpa .as .asia .associates .at .attorney .au .auction .audio .auto .autos .aw .ax .axa .az .azure .ba .band .bank .bar .barclaycard .barclays .bargains .bauhaus .bayern .bb .bbc .bbva .bd .be .beer .berlin .best .bf .bg .bh .bharti .bi .bible .bid .bike .bing .bingo .bio .biz .bj .black .blackfriday .bloomberg .blue .bm .bmw .bn .bnpparibas .bo .boats .bond .boo .boutique .br .bridgestone .broker .brother .brussels .bs .bt .budapest .build .builders .business .buzz .bv .bw .by .bz .bzh .ca .cab .cafe .cal .camera .camp .cancerresearch .canon .capetown .capital .caravan .cards .care .career .careers .cars .cartier .casa .cash .casino .cat .catering .cbn .cc .cd .center .ceo .cern .cf .cfa .cfd .cg .ch .channel .chat .cheap .chloe .christmas .chrome .church .ci .cisco .citic .city .ck .cl .claims .cleaning .click .clinic .clothing .club .cm .cn .co .coach .codes .coffee .college .cologne .com .community .company .computer .condos .construction .consulting .contractors .cooking .cool .coop .corsica .country .coupons .courses .cr .credit .creditcard .cricket .crown .crs .cruises .cu .cuisinella .cv .cw .cx .cy .cymru .cyou .cz .dabur .dad .dance .date .dating .datsun .day .dclk .de .deals .degree .delivery .democrat .dental .dentist .desi .design .dev .diamonds .diet .digital .direct .directory .discount .dj .dk .dm .dnp .do .docs .dog .doha .domains .doosan .download .drive .durban .dvag .dz .earth .eat .ec .edu .education .ee .eg .email .emerck .energy .engineer .engineering .enterprises .epson .equipment .er .erni .es .esq .estate .et .eu .eurovision .eus .events .everbank .exchange .expert .exposed .express .fail .faith .fan .fans .farm .fashion .feedback .fi .film .finance .financial .firmdale .fish .fishing .fit .fitness .fj .fk .flights .florist .flowers .flsmidth .fly .fm .fo .foo .football .forex .forsale .foundation .fr .frl .frogans .fund .furniture .futbol .fyi .ga .gal .gallery .garden .gb .gbiz .gd .gdn .ge .gent .genting .gf .gg .ggee .gh .gi .gift .gifts .gives .gl .glass .gle .global .globo .gm .gmail .gmo .gmx .gn .gold .goldpoint .golf .goo .goog .google .gop .gov .gp .gq .gr .graphics .gratis .green .gripe .gs .gt .gu .guge .guide .guitars .guru .gw .gy .hamburg .hangout .haus .healthcare .help .here .hermes .hiphop .hitachi .hiv .hk .hm .hn .hockey .holdings .holiday .homedepot .homes .honda .horse .host .hosting .hotmail .house .how .hr .ht .hu .ibm .icbc .icu .id .ie .ifm .il .im .immo .immobilien .in .industries .infiniti .info .ing .ink .institute .insure .int .international .investments .io .iq .ir .irish .is .it .iwc .java .jcb .je .jetzt .jewelry .jlc .jll .jm .jo .jobs .joburg .jp .juegos .kaufen .kddi .ke .kg .kh .ki .kim .kitchen .kiwi .km .kn .koeln .komatsu .kp .kr .krd .kred .kw .ky .kyoto .kz .la .lacaixa .land .lasalle .lat .latrobe .lawyer .lb .lc .lds .lease .leclerc .legal .lgbt .li .liaison .lidl .life .lighting .limited .limo .link .lk .loan .loans .lol .london .lotte .lotto .love .lr .ls .lt .ltda .lu .lupin .luxe .luxury .lv .ly .ma .madrid .maif .maison .management .mango .market .marketing .markets .marriott .mba .mc .md .me .media .meet .melbourne .meme .memorial .men .menu .mg .mh .miami .microsoft .mil .mini .mk .ml .mm .mma .mn .mo .mobi .moda .moe .monash .money .montblanc .mormon .mortgage .moscow .motorcycles .mov .movie .mp .mq .mr .ms .mt .mtn .mtpc .mu .museum .mv .mw .mx .my .mz .na .nadex .nagoya .name .navy .nc .ne .nec .net .network .neustar .new .news .nexus .nf .ng .ngo .nhk .ni .nico .ninja .nissan .nl .no .np .nr .nra .nrw .ntt .nu .nyc .nz .okinawa .om .one .ong .onl .online .ooo .oracle .org .organic .osaka .otsuka .ovh .pa .page .panerai .paris .partners .parts .party .pe .pf .pg .ph .pharmacy .philips .photo .photography .photos .physio .piaget .pics .pictet .pictures .pink .pizza .pk .pl .place .play .plumbing .plus .pm .pn .pohl .poker .porn .post .pr .praxi .press .pro .prod .productions .prof .properties .property .ps .pt .pub .pw .py .qa .qpon .quebec .racing .re .realtor .recipes .red .redstone .rehab .reise .reisen .reit .ren .rent .rentals .repair .report .republican .rest .restaurant .review .reviews .rich .rio .rip .ro .rocks .rodeo .rs .rsvp .ru .ruhr .run .rw .ryukyu .sa .saarland .sale .samsung .sandvik .sandvikcoromant .sap .sarl .saxo .sb .sc .sca .scb .schmidt .scholarships .school .schule .schwarz .science .scot .sd .se .seat .sener .services .sew .sex .sexy .sg .sh .shiksha .shoes .show .shriram .si .singles .site .sj .sk .ski .sky .sl .sm .sn .sncf .so .soccer .social .software .sohu .solar .solutions .sony .soy .space .spiegel .spreadbetting .sr .st .statoil .study .style .su .sucks .supplies .supply .support .surf .surgery .suzuki .sv .swiss .sx .sy .sydney .systems .sz .taipei .tatar .tattoo .tax .taxi .tc .td .team .tech .technology .tel .temasek .tennis .tf .tg .th .thd .theater .tickets .tienda .tips .tires .tirol .tj .tk .tl .tm .tn .to .today .tokyo .tools .top .toray .toshiba .tours .town .toys .tr .trade .trading .training .travel .trust .tt .tui .tv .tw .tz .ua .ug .uk .university .uno .uol .us .uy .uz .va .vacations .vc .ve .vegas .ventures .versicherung .vet .vg .vi .viajes .video .villas .vision .vlaanderen .vn .vodka .vote .voting .voto .voyage .vu .wales .walter .wang .watch .webcam .website .wed .wedding .weir .wf .whoswho .wien .wiki .williamhill .win .windows .wme .work .works .world .ws .wtc .wtf .xbox .xerox .xin .xn--1qqw23a .xn--30rr7y .xn--3bst00m .xn--3ds443g .xn--3e0b707e .xn--45brj9c .xn--45q11c .xn--4gbrim .xn--55qw42g .xn--55qx5d .xn--6frz82g .xn--6qq986b3xl .xn--80adxhks .xn--80ao21a .xn--80asehdb .xn--80aswg .xn--90a3ac .xn--90ais .xn--9et52u .xn--b4w605ferd .xn--c1avg .xn--cg4bki .xn--clchc0ea0b2g2a9gcd .xn--czr694b .xn--czrs0t .xn--czru2d .xn--d1acj3b .xn--d1alf .xn--estv75g .xn--fiq228c5hs .xn--fiq64b .xn--fiqs8s .xn--fiqz9s .xn--fjq720a .xn--flw351e .xn--fpcrj9c3d .xn--fzc2c9e2c .xn--gecrj9c .xn--h2brj9c .xn--hxt814e .xn--i1b6b1a6a2e .xn--imr513n .xn--io0a7i .xn--j1amh .xn--j6w193g .xn--kcrx77d1x4a .xn--kprw13d .xn--kpry57d .xn--kput3i .xn--l1acc .xn--lgbbat1ad8j .xn--mgb9awbf .xn--mgba3a4f16a .xn--mgbaam7a8h .xn--mgbab2bd .xn--mgbayh7gpa .xn--mgbbh1a71e .xn--mgbc0a9azcg .xn--mgberp4a5d4ar .xn--mgbpl2fh .xn--mgbx4cd0ab .xn--mxtq1m .xn--ngbc5azd .xn--node .xn--nqv7f .xn--nqv7fs00ema .xn--nyqy26a .xn--o3cw4h .xn--ogbpf8fl .xn--p1acf .xn--p1ai .xn--pgbs0dh .xn--q9jyb4c .xn--qcka1pmc .xn--rhqv96g .xn--s9brj9c .xn--ses554g .xn--unup4y .xn--vermgensberater-ctb .xn--vermgensberatung-pwb .xn--vhquv .xn--vuq861b .xn--wgbh1c .xn--wgbl6a .xn--xhq521b .xn--xkc2al3hye2a .xn--xkc2dl3a5ee0h .xn--y9a3aq .xn--yfro4i67o .xn--ygbi2ammx .xn--zfr164b .xxx .xyz .yachts .yandex .ye .yodobashi .yoga .yokohama .youtube .yt .za .zip .zm .zone .zuerich .zw'),
            true);

        $html = '';

        $position = 0;
        while (preg_match($rexUrlLinker, $text, $match, PREG_OFFSET_CAPTURE, $position)) {
            list($url, $urlPosition) = $match[0];

            // Add the text leading up to the URL.
            $html .= substr($text, $position, $urlPosition - $position);

            $scheme = $match[1][0];
            $username = $match[2][0];
            $password = $match[3][0];
            $domain = $match[4][0];
            $afterDomain = $match[5][0]; // everything following the domain
            $port = $match[6][0];
            $path = $match[7][0];

            // Check that the TLD is valid or that $domain is an IP address.
            $tld = strtolower(strrchr($domain, '.'));
            if (preg_match('{^\.[0-9]{1,3}$}', $tld) || isset($validTlds[$tld])) {
                // Do not permit implicit scheme if a password is specified, as
                // this causes too many errors (e.g. "my email:foo@example.org").
                if (!$scheme && $password) {
                    $html .= $username;

                    // Continue text parsing at the ':' following the "username".
                    $position = $urlPosition + strlen($username);
                    continue;
                }

                if (!$scheme && $username && !$password && !$afterDomain) {
                    // Looks like an email address.
                    $completeUrl = "mailto:$url";
                    $linkText = $url;
                } else {
                    // Prepend http:// if no scheme is specified
                    $completeUrl = $scheme ? $url : "http://$url";
                    $linkText = "$domain$port$path";
                }

                $linkHtml = '<a href="'.htmlspecialchars($completeUrl).'" target="_blank">'
                    .$linkText
                    .'</a>';

                // Cheap e-mail obfuscation to trick the dumbest mail harvesters.
                $linkHtml = str_replace('@', '&#64;', $linkHtml);

                // Add the hyperlink.
                $html .= $linkHtml;
            } else {
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

    public static function encrypt($text)
    {
        try {
            return trim(
                base64_encode(
                    gzdeflate(
                        mcrypt_encrypt(
                            MCRYPT_RIJNDAEL_256,
                            config('security.secret'),
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

    public static function decrypt($text)
    {
        if (empty($text)) {
            return false;
        }
        $oldErrRep = error_reporting(0);
        try {
            $decrypted = trim(
                mcrypt_decrypt(
                    MCRYPT_RIJNDAEL_256,
                    config('security.secret'),
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
            $firstDel = strpos($html, '<del>');
            $firstIns = strpos($html, '<ins>');
            if ($firstDel === false) {
                $firstDel = $firstIns;
            }
            if ($firstIns === false) {
                $firstIns = $firstDel;
            }
            $first = min($firstDel, $firstIns);
            $first = max(0, $first - $truncate);

            // find last change
            $lastDel = strrpos($html, '</del>');
            $lastIns = strrpos($html, '</ins>');
            if ($lastDel === false) {
                $lastDel = $lastIns;
            }
            if ($lastIns === false) {
                $lastIns = $lastDel;
            }
            $last = max($lastDel, $lastIns);
            $last = min(strlen($html), $last + $truncate);

            // create truncated string
            return ($first > 0 ? '<span>....</span> ' : '').substr($html, $first,
                $last - $first).($last < strlen($html) ? ' <span>....</span>' : '');
        }
    }

    public static function time2str($date, $today = false)
    {
        if (!$today) {
            $today = new DateTime();
            $today = $today->format('U');
        }
        $diff = $today - $date;
        if ($diff == 0) {
            return 'now';
        } elseif ($diff > 0) {
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 60) {
                    return 'just now';
                }
                if ($diff < 120) {
                    return '1 minute ago';
                }
                if ($diff < 3600) {
                    return floor($diff / 60).' minutes ago';
                }
                if ($diff < 7200) {
                    return '1 hour ago';
                }
                if ($diff < 86400) {
                    return floor($diff / 3600).' hours ago';
                }
            }
            if ($day_diff == 1) {
                return 'yesterday';
            }
            if ($day_diff < 7) {
                return $day_diff.' day'.($day_diff != 1 ? 's' : '').' ago';
            }
            if ($day_diff < 31) {
                return ceil($day_diff / 7).' week'.(ceil($day_diff / 7) != 1 ? 's' : '').' ago';
            }
            if ($day_diff < 60) {
                return 'last month';
            }

            return date('F Y', $date);
        } else {
            $diff = abs($diff);
            $day_diff = floor($diff / 86400);
            if ($day_diff == 0) {
                if ($diff < 120) {
                    return 'in a minute';
                }
                if ($diff < 3600) {
                    return 'in '.floor($diff / 60).' minutes';
                }
                if ($diff < 7200) {
                    return 'in an hour';
                }
                if ($diff < 86400) {
                    return 'in '.floor($diff / 3600).' hours';
                }
            }
            if ($day_diff == 1) {
                return 'tomorrow';
            }
            if ($day_diff < 4) {
                return date('l', $date);
            }
            if ($day_diff < 7 + (7 - date('w'))) {
                return 'next week';
            }
            if (ceil($day_diff / 7) < 4) {
                return 'in '.ceil($day_diff / 7).' week'.(ceil($day_diff / 7) != 1 ? 's' : '');
            }
            if (date('n', $date) == date('n') + 1) {
                return 'next month';
            }

            return date('F Y', $date);
        }
    }

    public static function toBytes($str)
    {
        $val = trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    public static function toSnakeCase($camelCase)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $camelCase, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }

        return implode('_', $ret);
    }
}
