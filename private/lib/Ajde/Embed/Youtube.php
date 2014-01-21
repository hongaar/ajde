<?php

class Ajde_Embed_Youtube extends Ajde_Embed
{
	public function convertUrlToEmbed() {
		if (substr($this->_code, 0, 7) == 'http://' || substr($this->_code, 0, 8) == 'https://') {
			$ytid = $this->_getYoutubeId();
			$this->_code = '<iframe width="425" height="700" src="http://www.youtube.com/embed/' . $ytid . '" frameborder="0" allowfullscreen></iframe>';
		}
	}
	
	public function getCode()
	{
		$this->convertUrlToEmbed();
		$this->_setHeight();
		$this->_setWidth();
		$this->_code = str_replace('" frameborder', '?rel=0&amp;autoplay=1&amp;wmode=transparent&amp;autohide=1" frameborder', $this->_code);
		return $this->_code;
	}
	
	private function _getYoutubeId()
	{
		if (substr($this->_code, 0, 15) == 'http://youtu.be') {
			return substr($this->_code, 16, 11);
		} else if (substr($this->_code, 0, 7) == 'http://' || substr($this->_code, 0, 8) == 'https://') {
			parse_str( parse_url( $this->_code, PHP_URL_QUERY ), $querystringArray );
			return $querystringArray['v'];
		} else {
			$matches = array();
			preg_match('%embed\/(.+?)[\/\?\"]%', $this->_code, $matches);
			return isset($matches[1]) ? substr($matches[1], 0, 11) : null;
		}
	}
	
	public function getThumbnail() {
		$ytid = $this->_getYoutubeId();
		if ($ytid) {
			$fullres = "http://img.youtube.com/vi/" . $ytid . "/maxresdefault.jpg";
			$ch = curl_init($fullres);			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_exec($ch);
			$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($http_status == 200) {
				return $fullres;
			} else {
				return "http://img.youtube.com/vi/" . $ytid . "/0.jpg";
			}
		} else {
			return null;
		}		
	}
}