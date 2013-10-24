<?php

abstract class Ajde_Publisher extends Ajde_Object_Standard
{
	private $_title;
	private $_image;
	private $_message;
	private $_url;
	
	public function setTitle($title) {
		$this->_title = $title;
	}
	
	public function setImage($image) {
		$this->_image = $image;
	}
	
	public function setMessage($message) {
		$this->_message = $message;
	}
	
	public function setUrl($url) {
		$this->_url = $url;
	}
	
	public function getTitle() {
		return $this->_title;
	}
	
	public function getImage() {
		return $this->_image;
	}
	
	public function getMessage() {
		return $this->_message;
	}
	
	public function getUrl() {
		return $this->_url;
	}
	
	abstract function publish();
}