<?php

class Ajde_Embed_Soundcloud extends Ajde_Embed
{
    public function convertUrlToEmbed()
    {
        if (substr($this->_code, 0, 7) == 'http://' || substr($this->_code, 0, 8) == 'https://') {
            $scurl = $this->_getSoundcloudUrl();
            //			$this->_code = '<object height="81" width="100%"> <param name="movie" value="https://player.soundcloud.com/player.swf?url="></param> <param name="allowscriptaccess" value="always"></param> <embed allowscriptaccess="always" height="81" src="https://player.soundcloud.com/player.swf?url='.urlencode($scurl).'" type="application/x-shockwave-flash" width="100%"></embed> </object>';
            $this->_code = '<iframe width="100%" height="166" scrolling="no" frameborder="no" src="http://w.soundcloud.com/player/?url='.urlencode($scurl).'&amp;auto_play=true&amp;show_artwork=true&amp;color=000"></iframe>';
        }
    }

    public function getCode()
    {
        $this->convertUrlToEmbed();
        $this->_setWidth();
        $this->stripTags();

        return $this->_code;
    }

    private function _getSoundcloudUrl()
    {
        if (substr($this->_code, 0, 7) == 'http://' || substr($this->_code, 0, 8) == 'https://') {
            $apiUrl = 'http://api.soundcloud.com/resolve.json?url='.urlencode($this->_code).'&client_id='.$this->_key();
            $hash = json_decode(Ajde_Http_Curl::get($apiUrl));

            return $hash->uri;
        } else {
            $matches = [];
            preg_match('%swf\?url=(.+?)[\/\?\"]%', $this->_code, $matches);

            return isset($matches[1]) ? urldecode($matches[1]) : null;
        }
    }

    public function getThumbnail()
    {
        if (substr($this->_code, 0, 7) == 'http://' || substr($this->_code, 0, 8) == 'https://') {
            $apiUrl = 'http://api.soundcloud.com/resolve.json?url='.urlencode($this->_code).'&client_id='.$this->_key();
            $hash = json_decode(Ajde_Http_Curl::get($apiUrl));

            return str_replace('large.jpg', 't300x300.jpg', $hash->artwork_url);
        } else {
            $url = $this->_getSoundcloudUrl();
            if ($url) {
                $apiUrl = $url.'.json?client_id='.$this->_key();
                $hash = json_decode(Ajde_Http_Curl::get($apiUrl));

                return str_replace('large.jpg', 't300x300.jpg', $hash->artwork_url);
            }
        }
    }

    private function _key()
    {
        return config('services.soundcloud.key');
    }
}
