<?php
/* 

	Color Space : v1.25 : 2009.04.20
	————————————————————————————————
			XYZ <-> Luv <-> LCHuv
			XYZ <-> Lab <-> LCHab
	RGB <-> XYZ <-> xyY
	RGB <-> HSL
	RGB <-> HSV <-> RYB
	RGB <-> CMY <-> CMYK
	RGB <-> HEX <-> STRING

*/

class ColorSpace{
	private $RYB_H = array();
	private $H_RYB = array();
	private $_XYZ_RGB = array();
	private $White;
	private $_xyY;

	function __construct(){
		$wheel = array(
			array(  0,   0), array( 15,   8), // Red
			array( 30,  17), array( 45,  26), // Orange
			array( 60,  34), array( 75,  41), // Yellow
			array( 90,  48), array(105,  54), // Lime
			array(120,  60), array(135,  81), // Green
			array(150, 103), array(165, 123), // Teal
			array(180, 138), array(195, 155), // Cyan
			array(210, 171), array(225, 187), // Azure
			array(240, 204), array(255, 219), // Blue
			array(270, 234), array(285, 251), // Indigo
			array(300, 267), array(315, 282), // Purple
			array(330, 298), array(345, 329), // Pink
			array(360,   0)
		);

		$a; $b; $i; $H; $_H; $H_;

		for($H = 0; $H < 360; $H++)
		{
			$H_ = false; $_H = false;

			for($i = 0; $i < 24; $i++)
			{
				$a = $wheel[$i]; $b = $wheel[$i+1];

				if($b && $b[1] < $a[1]) $b[1] += 360;

				if(!$H_ && $a[0] <= $H && $b[0] > $H)
				{
					$this->H_RYB[$H] = (($a[1]+($b[1]-$a[1])*($H-$a[0])/($b[0]-$a[0]))%360);
					$H_ = true;
				}

				if(!$_H && $a[1] <= $H && $b[1] > $H)
				{
					$this->RYB_H[$H] = (($a[0]+($b[0]-$a[0])*($H-$a[1])/($b[1]-$a[1]))%360);
					$_H = true;
				}

				if($H_ == true && _H == true)
					break;
			}
		}
		$this->White = $this->illuminant('2', 'D65');
		$this->profile('sRGB');
//		echo "this->White = ";
//		print_r($this->White);
	}
	function RYB_HSV($o)
	{
		$n = floor($o['H']); $x = $n > 0 ? $o['H'] % $n : 0;
		$a = $this->RYB_H[$n%360]; $b = $this->RYB_H[ceil($o['H'])%360];
		return array(
			'H' => $a + ($b - $a)*$x,
			'S' => $o['S'],
			'V' => $o['V']
			);
	}
	function HSV_RYB($o)
	{
		$n = floor($o['H']); $x = $n > 0 ? $o['H'] % $n : 0;
		$a = $this->H_RYB[$n % 360]; $b = $this->H_RYB[ceil($o['H']) % 360];
		return array(
			'H' => $a + ($b - $a) * $x,
			'S' => $o['S'],
			'V' => $o['V']
			);
	}
	function STRING_HEX($o)
	{
		return 0 + ('0x'.$o);
	}
	function HEX_STRING($o)
	{
		$str = sprintf("%X", $o); $n = strlen($str);
		while($n < 6){
			$str = '0' . $str; $n++;
		}
		return $str;
	}
	function HEX_RGB($o)
	{
		return array(
			'R' => ($o >> 16), 'G' => ($o >>  8) & 0xFF, 'B' => $o  & 0xFF
			);
	}
	function HEX32_RGBA($o)
	{
		return array(
			'R' => $o >> 16 & 0xFF, 'G' => $o >>  9 & 0xFF, 'B' => $o & 0xFF, 'A' => $o >> 24
			);
	}
	function RGBA_HEX32($o)
	{
		return ($o['A'] << 24 | $o['R'] << 16 | $o['G'] << 8 | $o['B']) >> 0;
	}
	function HEX32_rgbaa($o)
	{
		return 'rgba('. ($o >> 16 & 0xFF) . ','.
			($o >> 8 & 0xFF) . ','. ($o & 0xFF) . ','.
			(($o >> 24)/255).')';
	}
	/*
	function RGB_STRING($o)
	{
		return $this->HEX_STRING($this->RGB_HEX($o));
	}
	function RGB_rgbaa($o)
	{
		return 'rgba('. $o['R'] .','.$o['G'].','.$o['B'].','.$o['A'].')';
	}
	function HEX_rgba($o)
	{
		return $this->HEX32_rgbaa($o);
	}
	 */
	function RGB_HEX($o)
	{
		return $o['R'] << 16 | $o['G'] << 8 | $o['B'];
	}
	function RGB_CMY($o)
	{
		return array(
			'C' => 1 - ($o['R']/255), 'M' => 1 - ($o['G']/255), 'Y' => 1 - ($o['B']/255)
		);
	}
	function RGB_HSL($o)
	{
		$_R = $o['R'] / 255; $_G = $o['G'] / 255; $_B = $o['B'] / 255;
		$min = min($_R, $_G, $_B); $max = max($_R, $_G, $_B); $D = $max - $min;
		$L = ($max + $min) / 2;

		if($D == 0) { $H = 0; $S = 0;} // No Chroma

		else{
		
			if($L < 0.5)	$S = $D/ ($max + $min);
			else $S = $D/ (2-$max-$min);

			$DR = ( (($max-$_R)/6) + ($D/2) ) / $D;
			$DG = ( (($max-$_G)/6) + ($D/2) ) / $D;
			$DB = ( (($max-$_B)/6) + ($D/2) ) / $D;
			
			if($_R == $max) {	$H = $DB - $DG; }
			else if($_G==$max)	$H = (1/3) + $DR - $DB;
			else if($_B==$max)	$H = (2/3) + $DG - $DR;

			if($H < 0) $H += 1;
			if($H > 1) $H -= 1;
		}
		
		return array(
			'H' => $H * 360, 'S' => $S * 100, 'L' => $L * 100
			);
	}
	function RGB_HSV($o)
	{
		$_R = $o['R']/255; $_G = $o['G']/255; $_B = $o['B']/255;
	
		$min = min($_R,$_G,$_B); $max = max($_R,$_G,$_B); $D = $max-$min; 
		$V = $max;
	
		if($D==0) { $H = 0; $S = 0; } // No chroma

		else { // Chromatic data

			$S = $D/$max;
    
			$DR = ( (($max-$_R)/6) + ($D/2) ) / $D;
			$DG = ( (($max-$_G)/6) + ($D/2) ) / $D;
			$DB = ( (($max-$_B)/6) + ($D/2) ) / $D;

			if($_R==$max)      $H = $DB-$DG;
			else if($_G==$max) $H = (1/3) + $DR-$DB;
			else if($_B==$max) $H = (2/3) + $DG-$DR;

			if($H<0) $H += 1;
			if($H>1) $H -= 1;
		}

		return array( 'H'=> $H*360, 'S'=> $S*100, 'V'=> $V*100 );
	}
	function RGB_XYZ($o)
	{
		$M = $this->_RGB_XYZ; $z = array(); 
	
		$R = $o['R'] / 255; $G = $o['G'] / 255; $B = $o['B'] / 255; 

		if($this->_Space == 'sRGB') { 
			$R = ($R > 0.04045) ? pow((($R + 0.055) / 1.055), 2.4) : $R / 12.92; 
			$G = ($G > 0.04045) ? pow((($G + 0.055) / 1.055), 2.4) : $G / 12.92; 
			$B = ($B > 0.04045) ? pow((($B + 0.055) / 1.055), 2.4) : $B / 12.92; 
		}
		else { 
			$R = pow($R, $this->_Gamma); 
			$G = pow($G, $this->_Gamma); 
			$B = pow($B, $this->_Gamma); 
		}

		$z['X'] = $R * $M[0] + $G * $M[3] + $B * $M[6];
		$z['Y'] = $R * $M[1] + $G * $M[4] + $B * $M[7];
		$z['Z'] = $R * $M[2] + $G * $M[5] + $B * $M[8];

		return $z;
	}

	function CMY_RGB ($o) { 
		
		return array( 'R' => max(0, (1 - $o['C']) * 255), 'G' => max(0, (1 - $o['M']) * 255), 'B' => max(0, (1 - $o['Y']) * 255) );

	}

	function CMY_CMYK ($o) { 

		$C = $o['C']; $M = $o['M']; $Y = $o['Y']; $K = min($Y, min($M, min($C, 1))); 

		$C = round(($C - $K) / (1 - $K) * 100); 
		$M = round(($M - $K) / (1 - $K) * 100); 
		$Y = round(($Y - $K) / (1 - $K) * 100); 
		$K = round($K * 100); 

		return array( 'C' => $C, 'M' => $M, 'Y' => $Y, 'K' => $K );

	} 

	// CMYK = C: Cyan / M: Magenta / Y: Yellow / K: Key (black)

	function CMYK_CMY ($o) { 

		return array( 'C' => ($o['C'] * (1 - $o['K']) + $o['K']), 'M' => ($o['M'] * (1 - $o['K']) + $o['K']), 'Y' => ($o['Y'] * (1 - $o['K']) + $o['K']) );

	} 

	// HSL (1978) = H: Hue / S: Saturation / L: Lightess

	function Hue_2_RGB( $v1, $v2, $vH ) {
		
		if($vH < 0) $vH += 1; 
		if($vH > 1) $vH -= 1; 
		if((6 * $vH) < 1) return $v1 + ($v2 - $v1) * 6 * $vH; 
		if((2 * $vH) < 1) return $v2; 
		if((3 * $vH) < 2) return $v1 + ($v2 - $v1) * ((2 / 3) - $vH) * 6; 
		
		return $v1; 

	}

	function HSL_RGB ($o) { 

		$H = $o['H'] / 360; $S = $o['S'] / 100; $L = $o['L'] / 100; $R; $G; $B; $_1; $_2; 

		if($S == 0) { // HSL from 0 to 1

			$R = $L * 255; $G = $L * 255; $B = $L * 255; 

		}
		else {
		
			if($L < 0.5) $_2 = $L * (1 + $S); 
			else $_2 = ($L + $S) - ($S * $L); 
			
			$_1 = 2 * $L - $_2; 
			
			$R = 255 * $this->Hue_2_RGB($_1, $_2, $H + (1 / 3)); 
			$G = 255 * $this->Hue_2_RGB($_1, $_2, $H); 
			$B = 255 * $this->Hue_2_RGB($_1, $_2, $H - (1 / 3)); 

		}
		
		return array( 'R' => $R, 'G' => $G, 'B' => $B );

	} 

	// HSV (1978) = H: Hue / S: Saturation / V: Value

	function HSV_RGB ($o) { 

		$H = $o['H'] / 360; $S = $o['S'] / 100; $V = $o['V'] / 100; $R; $G; $B; 
		
		if($S == 0) { $R = $G = $B = round($V * 255); }

		else { if($H >= 1) $H = 0; 

			$H = 6 * $H; 
			$D = $H - floor($H); 
			$A = round(255 * $V * (1 - $S)); 
			$B = round(255 * $V * (1 - ($S * $D))); 
			$C = round(255 * $V * (1 - ($S * (1 - $D)))); 
			$V = round(255 * $V); 

			switch(floor($H)) { 

				case 0: $R = $V; $G = $C; $B = $A; break; 
				case 1: $R = $B; $G = $V; $B = $A; break; 
				case 2: $R = $A; $G = $V; $B = $C; break; 
				case 3: $R = $A; $G = $B; $B = $V; break; 
				case 4: $R = $C; $G = $A; $B = $V; break; 
				case 5: $R = $V; $G = $A; $B = $B; break; 

			}
		}

		return array( 'R' => $R, 'G' => $G, 'B' => $B );

	} 

	// CIE (Commission International de L’Eclairage)

	// CIE-XYZ (1931) = Y: Luminescence / XZ: Spectral Weighting Curves (Spectral Locus)

	function XYZ_RGB ($o) {
		$M = $this->_XYZ_RGB; $z = array(); 

		$z['R'] = $o['X'] * $M[0] + $o['Y'] * $M[3] + $o['Z'] * $M[6];
		$z['G'] = $o['X'] * $M[1] + $o['Y'] * $M[4] + $o['Z'] * $M[7];
		$z['B'] = $o['X'] * $M[2] + $o['Y'] * $M[5] + $o['Z'] * $M[8];

		if($this->_Space == 'sRGB') { 

			$z['R'] = ($z['R'] > 0.0031308) ? (1.055 * pow($z['R'], 1 / 2.4)) - 0.055 : 12.92 * $z['R']; 
			$z['G'] = ($z['G'] > 0.0031308) ? (1.055 * pow($z['G'], 1 / 2.4)) - 0.055 : 12.92 * $z['G']; 
			$z['B'] = ($z['B'] > 0.0031308) ? (1.055 * pow($z['B'], 1 / 2.4)) - 0.055 : 12.92 * $z['B']; 

		}
		else { 

			$z['R'] = pow($z['R'], 1 / $this->_Gamma); 
			$z['G'] = pow($z['G'], 1 / $this->_Gamma); 
			$z['B'] = pow($z['B'], 1 / $this->_Gamma); 

		}

		return array( 'R' => round($z['R'] * 255), 'G' => round($z['G'] * 255), 'B' => round($z['B'] * 255) );

	} 

	function XYZ_xyY ($o) {

		$n = $o['X'] + $o['Y'] + $o['Z'];
		
		if($n == 0) return array( 'x' => 0, 'y' => 0, 'Y' => $o['Y'] );

		return array( 'x' => $o['X'] / $n, 'y' => $o['Y'] / $n, 'Y' => $o['Y'] );

	}

	function XYZ_HLab ($o) {

		$n = sqrt($o['Y']); 

		return array( 'L' => 10 * $n, 'a' => 17.5 * (((1.02 * $o['X']) - $o['Y']) / $n), 'b' => 7 * (($o['Y'] - (0.847 * $o['Z'])) / $n) );

	}

	function XYZ_Lab ($o) {
		$r = $this->White; 

		function fu($n) { if($n > 0.008856) { return pow($n, 1 / 3); } else { return (7.787 * $n) + (16 / 116); } }

		$X = fu($o['X'] / $r['X']); $Y = fu($o['Y'] / $r['Y']); $Z = fu($o['Z'] / $r['Z']); 

		return array( 'L' => (116 * $Y) - 16, 'a' => 500 * ($X - $Y), 'b' => 200 * ($Y - $Z) );

	}

	function XYZ_Luv ($o) {
		$r = $this->White; 

		$U = (4 * $o['X']) / ($o['X'] + (15 * $o['Y']) + (3 * $o['Z'])); $V = (9 * $o['Y']) / ($o['X'] + (15 * $o['Y']) + (3 * $o['Z'])); 
		
		if($o['Y'] > 0.008856) { $o['Y'] = pow($o['Y'], 1 / 3); } else { $o['Y'] = (7.787 * $o['Y']) + (16 / 116); }
		
		$_L = (116 * $o['Y']) - 16; $_U = (4 * $r['X']) / ($r['X'] + (15 * $r['Y']) + (3 * $r['Z'])); $_V = (9 * $r['Y']) / ($r['X'] + (15 * $r['Y']) + (3 * $r['Z']));

		return array( 'L' => $_L, 'u' => 13 * $_L * ($U - $_U), 'v' => 13 * $_L * ($V - $_V) );

	}

	// CIE-xyY (1931) = Y: Luminescence / xy: Chromaticity Co-ordinates (Spectral Locus)

	function xyY_XYZ ($o) { 

		return array( 'X' => ($o['x'] * $o['Y']) / $o['y'], 'Y' => $o['Y'], 'Z' => ((1 - $o['x'] - $o['y']) * $o['Y']) / $o['y'] ); 

	}

	// Hunter-L*ab (1948) = L: Lightness / ab: Color-opponent Dimensions

	function HLab_XYZ ($o) { 

		$_Y = $o['L'] / 10; $_X = ($o['a'] / 17.5) * ($o['L'] / 10); $_Z = ($o['b'] / 7) * ($o['L'] / 10); 

		$Y = pow($_Y, 2); $X = ($_X + $Y) / 1.02; $Z = -1 * ($_Z - $Y) / 0.847; 

		return array( 'X' => $X, 'Y' => $Y, 'Z' => $Z );

	}

	// CIE-L*ab (1976) = L: Luminescence / a: Red / Green / b: Blue / Yellow

	function Lab_XYZ ($o) {
		$r = $this->White; 
		
		$Y = ($o['L'] + 16) / 116; $X = $o['a'] / 500 + $Y; $Z = $Y - $o['b'] / 200; 

		$Y = pow($Y, 3) > 0.008856 ? pow($Y, 3) : ($Y - 16 / 116) / 7.787; 
		$X = pow($X, 3) > 0.008856 ? pow($X, 3) : ($X - 16 / 116) / 7.787; 
		$Z = pow($Z, 3) > 0.008856 ? pow($Z, 3) : ($Z - 16 / 116) / 7.787; 

		return array( 'X' => $r['X'] * $X, 'Y' => $r['Y'] * $Y, $Z => $r['Z'] * $Z );

	}

	function Lab_LCHab ($o) { 

		$H = atan2($o['b'], $o['a']) * (180 / PI); 
		
		if($H < 0) { $H += 360; } else if($H > 360) { $H -= 360; }
		
		return array( 'L' => $o['L'], 'C' =>sqrt($o['a'] * $o['a'] + $o['b'] * $o['b']), 'H'=> $H );

	}

	// CIE-L*uv (1976) = L: Luminescence / u: Saturation / v: Hue

	function Luv_XYZ ($o) {
		$r = $White; 

		$Y = ($o['L'] + 16) / 116; $Y = (pow($Y, 3) > 0.008856) ? pow($Y, 3) : (($Y - 16 / 116) / 7.787); 

		$_U = (4 * $r['X']) / ($r['X'] + (15 * $r['Y']) + (3 * $r['Z'])); $_V = (9 * $r['Y']) / ($r['X'] + (15 * $r['Y']) + (3 * $r['Z'])); 

		$U = $o['u'] / (13 * $o['L']) + $_U; $V = $o['v'] / (13 * $o['L']) + $_V; 
		
		$X = - (9 * $Y * $U) / (($U - 4) * $V - $U * $V); $Z = (9 * $Y - (15 * $V * $Y) - ($V * $X)) / (3 * $V); 

		return array( 'X' => $X, 'Y' => $Y, 'Z' => $Z );

	}

	function Luv_LCHuv ($o) {
		$H = atan2($o['v'], $o['u']) * (180 / PI); 

		if($H < 0) { $H += 360; } else if($H > 360) { $H -= 360; }

		return array( 'L'=> $o['L'], 'C' => sqrt($o['u'] * $o['u'] + $o['v'] * $o['v']), 'H' => $H );

	}

	// CIE-L*CH (1986) = L: Luminescece / C: Chromacity / H: Hue

	function LCHab_Lab ($o) { 

		$rad = $o['H'] * (PI / 180); 

		return array( 'L' => $o['L'], 'a' => cos($rad) * $o['C'], 'b' => sin($rad) * $o['C'] );

	}

	function LCHuv_Luv ($o) { 

		$rad = $o['H'] * (PI / 180); 

		return array( 'L' => $o['L'], 'u' => cos($rad) * $o['C'], 'v' => sin($rad) * $o['C'] );

	}

	function adapt($o, $type)
	{
		$r = array( // Adaption methods
			'XYZ scaling' => array( 
				'A' =>  array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 1) ), 
				'Z' =>  array(array(1, 0, 0), array(0, 1, 0), array(0, 0, 1) ) ), 
			'Von Kries' => array( 
				'A' =>  array(array(0.400240, -0.226300, 0), array( 0.707600, 1.165320, 0), array(-0.080810,  0.045700, 0.918220) ), 
				'Z' =>  array(array(1.859936,  0.361191, 0), array(-1.129382, 0.638812, 0), array( 0.219897, -0.000006, 1.089064) ) ), 
			'Bradford' => array( 
				'A' =>  array(array(0.895100,  0.26640000, -0.16139900), array(-0.75019900, 1.71350, 0.0367000), array( 0.03889900, -0.0685000, 1.02960000) ), 
				'Z' =>  array(array(0.986993, -0.14705399,  0.15996299), array( 0.43230499, 0.51836, 0.0492912), array(-0.00852866,  0.0400428, 0.96848699) ) ) ); 
		
		$WS = $this->_xyY; $WD = $this->White; // White Point Source + Destination
//		echo 'WS = ';
//		print_r($WS);
//		echo 'WD = ';
//		print_r($WD);

		$A = $r[$type]['A']; $Z = $r[$type]['Z']; // Load Matrices

		$CRD = $this->multiply($A, array( array($WD['X']), array($WD['Y']), array($WD['Z']) )); // Convert to cone responce domain
		$CRS = $this->multiply($A, array( array($WS['X']), array($WS['Y']), array($WS['Z']) ));
		//print_r($CRD);
		//print_r($CRS);

		$M = array( array($CRD[0][0] / $CRS[0][0], 0, 0), array(0, $CRD[1][0] / $CRS[1][0], 0), array(0, 0, $CRD[2][0] / $CRS[2][0]) ); // Scale Vectors

		$z = $this->multiply($Z, $this->multiply($M, $this->multiply($A, array( array($o['X']), array($o['Y']), array($o['Z']) )))); // Back to XYZ

		return array( 'X' => $z[0][0], 'Y' => $z[1][0], 'Z' => $z[2][0] );
	}

	function f($o)
	{
		$x = $this->xyY_XYZ($o);
//		echo 'o = ';
//		print_r($o);
//		echo 'x = ';
//		print_r($x);
		return $this->adapt($x, 'Bradford');
	}
	function illuminant($observer, $type) { 

		$o = $this->_illuminant[$type];

		$o = ($observer == 2) ? array( 'x' => $o[0], 'y' => $o[1], 'Y' => 1 ) : array( 'x' => $o[2], 'y' => $o[3], 'Y' => 1 );
		//print_r($o);

		return $this->xyY_XYZ($o);

	}
	private $_Space;
	private $_Gamma;
	private $_White;
	private $_Matrix;

	function profile($i)
	{
		$m = $this->_profile[$i];
		//print_r($m);

		$this->_Space = $i; 
		$this->_Gamma = $m[0]; 
		$this->_White = $m[1]; 
		$this->_Matrix = $m;

		// Input Illuminant
	
		$this->_xyY = $this->illuminant('2', $m[1]); 
		//print_r($this->_xyY);
		
		
		$R = $this->f(array( 'x' => $m[2], 'y' => $m[3], 'Y' => $m[4] )); 
		$G = $this->f(array( 'x' => $m[5], 'y' => $m[6], 'Y' => $m[7] )); 
		$B = $this->f(array( 'x' => $m[8], 'y' => $m[9], 'Y' => $m[10] )); 
//		print_r($R);
//		print_r($G);
//		print_r($B);

		$this->_RGB_XYZ = array( $R['X'], $R['Y'], $R['Z'],  $G['X'], $G['Y'], $G['Z'],  $B['X'], $B['Y'], $B['Z'] );
//		print_r($this->_RGB_XYZ);

		$this->_XYZ_RGB = $this->inverse($this->_RGB_XYZ);
	}
	private $_RGB_XYZ;

	private $_profile = array( // [ Gamma, Illuminant, Matrix ]
		'Adobe (1998)'=>   array( 2.2,  'D65', 0.64, 0.33, 0.297361, 0.21, 0.71, 0.627355, 0.15, 0.06, 0.075285 ), // Adobe
		'Apple RGB'=>      array( 1.8,  'D65', 0.625, 0.34, 0.244634, 0.28, 0.595, 0.672034, 0.155, 0.07, 0.083332 ), // Apple, a.k.a. SGI
		'BestRGB'=>        array( 2.2,  'D50', 0.7347, 0.2653, 0.228457, 0.215, 0.775, 0.737352, 0.13, 0.035, 0.034191 ), // Don Hutcheson
		'Beta RGB'=>       array( 2.2,  'D50', 0.6888, 0.3112, 0.303273, 0.1986, 0.7551, 0.663786, 0.1265, 0.0352, 0.032941 ), // Bruce Lindbloom
		'Bruce RGB'=>      array( 2.2,  'D65', 0.64, 0.33, 0.240995, 0.28, 0.65, 0.683554, 0.15, 0.06, 0.075452 ), // Bruce Fraser
		'CIE RGB'=>        array( 2.2,    'E', 0.735, 0.265, 0.176204, 0.274, 0.717, 0.812985, 0.167, 0.009, 0.010811 ), // CIE
		'ColorMatch'=>     array( 1.8,  'D50', 0.63, 0.34, 0.274884, 0.295, 0.605, 0.658132, 0.15, 0.075, 0.066985 ), // Radius
		'DonRGB4'=>        array( 2.2,  'D50', 0.696, 0.3, 0.27835, 0.215, 0.765, 0.68797, 0.13, 0.035, 0.03368 ), 
		'eciRGB'=>         array( 1.8,  'D50', 0.67, 0.33, 0.32025, 0.21, 0.71, 0.602071, 0.14, 0.08, 0.077679 ), // European Colour Initiative
		'Ekta Space PS5'=> array( 2.2,  'D50', 0.695, 0.305, 0.260629, 0.26, 0.7, 0.734946, 0.11, 0.005, 0.004425 ), // Joseph Holmes
		'Generic RGB'=>    array( 1.8,  'D65', 0.6295, 0.3407, 0.232546, 0.2949, 0.6055, 0.672501, 0.1551, 0.0762, 0.094952 ), 
		'HDTV (HD-CIF)'=>  array( 1.95, 'D65', 0.64, 0.33, 0.212673, 0.3, 0.6, 0.715152, 0.15, 0.06, 0.072175 ), // a.k.a. ITU-R BT.701
		'NTSC'=>           array( 2.2,    'C', 0.67, 0.33, 0.298839, 0.21, 0.71, 0.586811, 0.14, 0.08, 0.11435 ), // National Television System Committee (NTSC), a.k.a. Y'I'Q'
		'PAL / SECAM'=>    array( 2.2,  'D65', 0.64, 0.33, 0.222021, 0.29, 0.6, 0.706645, 0.15, 0.06, 0.071334 ), // European Broadcasting Union (EBU), a.k.a. Y'U'V'
		'ProPhoto'=>       array( 1.8,  'D50', 0.7347, 0.2653, 0.28804, 0.1596, 0.8404, 0.711874, 0.0366, 0.0001, 0.000086 ), // Kodak, a.k.a. ROMM RGB
		'SGI'=>            array( 1.47, 'D65', 0.625, 0.34, 0.244651, 0.28, 0.595, 0.672030, 0.155, 0.07, 0.083319 ), 
		'SMPTE-240M'=>     array( 1.92, 'D65', 0.63, 0.34, 0.212413, 0.31, 0.595, 0.701044, 0.155, 0.07, 0.086543 ), 
		'SMPTE-C'=>        array( 2.2,  'D65', 0.63, 0.34, 0.212395, 0.31, 0.595, 0.701049, 0.155, 0.07, 0.086556 ), // Society of Motion Picture and Television Engineers (SMPTE)
		'sRGB'=>           array( 2.2,  'D65', 0.64, 0.33, 0.212656, 0.3, 0.6, 0.715158, 0.15, 0.06, 0.072186 ), // Microsoft & Hewlett - Packard
		'Wide Gamut'=>     array( 2.2,  'D50', 0.7347, 0.2653, 0.258187, 0.1152, 0.8264, 0.724938, 0.1566, 0.0177, 0.016875 ) // Adobe
	);

	private $_illuminant = array( // [ x2°, y2°, x10°, y10°, CCT (Kelvin) ]
	
	'A'=>   array( 0.44757, 0.40745, 0.45117, 0.40594, 2856 ),  // Incandescent tungsten
	'B'=>   array( 0.34842, 0.35161, 0.3498 , 0.3527 , 4874 ),  // Obsolete, direct sunlight at noon 
	'C'=>   array( 0.31006, 0.31616, 0.31039, 0.31905, 6774 ),  // Obsolete, north sky daylight 
	'D50'=> array( 0.34567, 0.35850, 0.34773, 0.35952, 5003 ),  // ICC Profile PCS. Horizon light. 
	'D55'=> array( 0.33242, 0.34743, 0.33411, 0.34877, 5503 ),  // Compromise between incandescent and daylight
	'D65'=> array( 0.31271, 0.32902, 0.31382, 0.33100, 6504 ),  // Daylight, sRGB color space 
	'D75'=> array( 0.29902, 0.31485, 0.29968, 0.31740, 7504 ),  // North sky day light
	'E'=>   array( 0.33333, 0.33333, 0.33333, 0.33333, 5454 ),  // Equal energy 
	'F1'=>  array( 0.31310, 0.33727, 0.31811, 0.33559, 6430 ),  // Daylight Fluorescent 
	'F2'=>  array( 0.37208, 0.37529, 0.37925, 0.36733, 4230 ),  // Cool White Fluorescent 
	'F3'=>  array( 0.40910, 0.39430, 0.41761, 0.38324, 3450 ),  // White Fluorescent 
	'F4'=>  array( 0.44018, 0.40329, 0.44920, 0.39074, 2940 ),  // Warm White Fluorescent 
	'F5'=>  array( 0.31379, 0.34531, 0.31975, 0.34246, 6350 ),  // Daylight Fluorescent 
	'F6'=>  array( 0.37790, 0.38835, 0.38660, 0.37847, 4150 ),  // Lite White Fluorescent
	'F7'=>  array( 0.31292, 0.32933, 0.31569, 0.32960, 6500 ),  // D65 simulator, day light simulator 
	'F8'=>  array( 0.34588, 0.35875, 0.34902, 0.35939, 5000 ),  // D50 simulator, Sylvania F40 Design 
	'F9'=>  array( 0.37417, 0.37281, 0.37829, 0.37045, 4150 ),  // Cool White Deluxe Fluorescent 
	'F10'=> array( 0.34609, 0.35986, 0.35090, 0.35444, 5000 ),  // Philips TL85, Ultralume 50 
	'F11'=> array( 0.38052, 0.37713, 0.38541, 0.37123, 4000 ),  // Philips TL84, Ultralume 40 
	'F12'=> array( 0.43695, 0.40441, 0.44256, 0.39717, 3000 ) ); // Philips TL83, Ultralume 30


	function multiply($m1, $m2) {
		$ni = count($m1); $ki = $ni; $i; $nj; $kj = count($m2[0]); $j;
		$cols = count($m1[0]); $M = array(); $sum; $nc; $c;
		do { $i = $ki - $ni;
		  $M[$i] = array();
		  $nj = $kj;
		  do { $j = $kj - $nj;
			$sum = 0;
			$nc = $cols;
			do { $c = $cols - $nc;
			  $sum += $m1[$i][$c] * $m2[$c][$j];
			} while(($nc -= 1));
			$M[$i][$j] = $sum;
		  } while(($nj -= 1));
		} while(($ni -= 1));
		return $M;
	}

	function determinant($m) { // 3x3

//		print_r($m);
		return $m[0] * ($m[4] * $m[8] - $m[5] * $m[7]) -
			   $m[1] * ($m[3] * $m[8] - $m[5] * $m[6]) +
			   $m[2] * ($m[3] * $m[7] - $m[4] * $m[6]);

	}

	function inverse($m) { // 3x3
		
		$d = 1.0 / $this->determinant($m);
		
		return array( $d * ($m[4] * $m[8] - $m[5] * $m[7]),
				 $d * (-1 * ($m[1] * $m[8] - $m[2] * $m[7])),
				 $d * ($m[1] * $m[5] - $m[2] * $m[4]),
		
				 $d * (-1 * ($m[3] * $m[8] - $m[5] * $m[6])),
				 $d * ($m[0] * $m[8] - $m[2] * $m[6]),
				 $d * (-1 * ($m[0] * $m[5] - $m[2] * $m[3])),
		
				 $d * ($m[3] * $m[7] - $m[4] * $m[6]),
				 $d * (-1 * ($m[0] * $m[7] - $m[1] * $m[6])),
				 $d * ($m[0] * $m[4] - $m[1] * $m[3])
			 );

	}
};
?>
