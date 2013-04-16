<?php

class Ajde_Embed_Mixcloud extends Ajde_Embed
{
	
	public function convertUrlToEmbed() {
		if (substr($this->_code, 0, 7) == 'http://' || substr($this->_code, 0, 8) == 'https://') {			
			$this->_code = '<div><object width="480" height="480"><param name="movie" value="http://www.mixcloud.com/media/swf/player/mixcloudLoader.swf?feed='.urlencode($this->_code).'&embed_type=widget_standard"></param><param name="allowFullScreen" value="true"></param><param name="wmode" value="opaque"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.mixcloud.com/media/swf/player/mixcloudLoader.swf?feed='.urlencode($this->_code).'&embed_type=widget_standard" type="application/x-shockwave-flash" wmode="opaque" allowscriptaccess="always" allowfullscreen="true" width="480" height="480"></embed></object><div style="clear:both; height:3px;"></div><div style="clear:both; height:3px;"></div></div>';
		}
	}
	
	public function getCode()
	{
		$this->convertUrlToEmbed();
		$this->_setHeight();
		$this->_setWidth();
		$this->stripTags();
		return $this->_code;
	}
	
	private function _getMixcloudUrl()
	{
		if (substr($this->_code, 0, 7) == 'http://' || substr($this->_code, 0, 8) == 'https://') {
			return $this->_code;
		} else {
			$matches = array();
			preg_match('%mixcloudLoader\.swf\?feed=(.+?)&embed_uuid%', $this->_code, $matches);
			return isset($matches[1]) ? urldecode($matches[1]) : null;
		}
	}
	
	public function getThumbnail() {
		$url = $this->_getMixcloudUrl();
		if ($url) {
			$html = Ajde_Http_Curl::get($url);
			$matches = array();
			preg_match('%og:image\" content=\"(.+?)\"%', $html, $matches);		
			// we get .png, we want .jpg
			if (isset($matches[1])) {
				return $matches[1];
			}
			return null;
		}
		return null;
	}	
}