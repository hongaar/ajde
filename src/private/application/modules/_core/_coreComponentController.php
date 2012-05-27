<?php

class _coreComponentController extends Ajde_Controller
{
	/************************
	 * Ajde_Component_Resource
	 ************************/
	
	function resourceLocalDefault()
	{
		return $this->_getLocalResource();
	}

	function resourceCompressedDefault()
	{
		return $this->_getCompressedResource();
	}

	protected function _getLocalResource()
	{
		return $this->_getResource('Ajde_Resource_Local');
	}

	protected function _getCompressedResource()
	{
		return $this->_getResource('Ajde_Resource_Local_Compressed');
	}

	protected function _getResource($className)
	{
		// get resource from request
		$fingerprint = Ajde::app()->getRequest()->getRaw('id');
		if (!Ajde_Core_Autoloader::exists($className)) {
			throw new Ajde_Controller_Exception("Resource type could not be loaded");
		}
		//$resource = call_user_func_array(array($className,"fromHash"), array($hash));
		$resource = call_user_func_array(array($className,"fromFingerprint"), array($this->getFormat(), $fingerprint));
		return $resource->getContents();
	}
	
	/************************
	 * Ajde_Component_Form
	 ************************/

	public function formDefault()
	{
		if ($this->getAction() !== 'form/ajax') {
			$this->setAction('form/form');
		}
		
		// CSRF
		Ajde::app()->getDocument()->getLayout()->requireTimeoutWarning();
		$formToken = Ajde::app()->getRequest()->getFormToken();		
		$this->getView()->assign('formToken', $formToken);
		
		$this->getView()->assign('formAction', $this->getFormAction());		
		$this->getView()->assign('formId', $this->getFormId());
		$this->getView()->assign('extraClass', $this->getExtraClass());
		$this->getView()->assign('innerXml', $this->getInnerXml());
		return $this->render();
	}
	 
	public function formAjaxDefault()
	{
		$this->setAction('form/ajax');
		$this->getView()->assign('formFormat', $this->getFormFormat());
		return $this->formDefault();
	}

	public function formUploadHtml()
	{
		$options = $this->getOptions();
		$optionsId = md5(serialize($options));
		$session = new Ajde_Session('AC.Form');
		$session->set($optionsId, $options);

		$this->setAction('form/upload');
		$this->getView()->assign('name', $this->getName());
		$this->getView()->assign('optionsId', $optionsId);
		$this->getView()->assign('optionsMultiple', issetor($options['multiple'], false));
		$this->getView()->assign('inputId', $this->getInputId());
		$this->getView()->assign('extraClass', $this->getExtraClass());
		return $this->render();
	}
	
	public function formUploadJson()
	{
		$optionsId = Ajde::app()->getRequest()->getParam('optionsId', array());
		$session = new Ajde_Session('AC.Form');
		$options = $session->get($optionsId);
		
		// Load UploadHelper.php
		$helper = new Ajde_Component_Form_UploadHelper();
		
		$saveDir = $options['saveDir'];
		$allowedExtensions = $options['extensions'];
		
		// max file size in bytes
		$max_upload = (int)(ini_get('upload_max_filesize'));
		$max_post = (int)(ini_get('post_max_size'));
		$memory_limit = (int)(ini_get('memory_limit'));
		$upload_mb = min($max_upload, $max_post, $memory_limit);
		$sizeLimit = $upload_mb * 1024 * 1024;
		
		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$result = $uploader->handleUpload($saveDir);
		
		// Set content type to text/html for qqUploader bug 163
		// @see https://github.com/valums/file-uploader/issues/163
		Ajde::app()->getDocument()->setContentType('text/html');
		
		// to pass data through iframe you will need to encode all html tags
		return $result;
	}
	
	/************************
	 * Ajde_Component_Image
	 ************************/
	
	public function imageHtml() {
		/* @var $image Ajde_Resource_Image */
		$image = $this->getImage();
		
		//$session = new Ajde_Session('AC.Image');
		//$session->set($imageId, $image);
				
		$this->setAction('image/show');
		$this->getView()->assign('href', $image->getLinkUrl());
		$this->getView()->assign('width', $image->getWidth());
		$this->getView()->assign('height', $image->getHeight());
		$this->getView()->assign('extraClass', $this->getExtraClass());
		return $this->render();
	}
	
	public function imageBase64Html() {		
		$image = $this->getImage();
		
		// TODO: add crop/resize option
		$image->crop($image->getHeight(), $image->getWidth());		
		
		$this->setAction('image/base64');
		$this->getView()->assign('image', $this->getImage());
		$this->getView()->assign('width', $this->getWidth());
		$this->getView()->assign('height', $this->getHeight());
		$this->getView()->assign('extraClass', $this->getExtraClass());
		return $this->render();
	}
	
	public function imageData() {
		$fingerprint = Ajde::app()->getRequest()->getRaw('id');
		$image = Ajde_Resource_Image::fromFingerprint($fingerprint);
		
		//$session = new Ajde_Session('AC.Image');
		//if (!$session->has($imageId)) {
			//Ajde::app()->getResponse()->redirectNotFound();
		//}
		
		/* @var $image Ajde_Resource_Image */
		//$image = $session->get($imageId);
		//$image = $session->getOnce($imageId);
				
		// TODO: add crop/resize option
		$image->crop($image->getHeight(), $image->getWidth());
		
		Ajde::app()->getDocument()->setContentType($image->getMimeType());
		$output = $image->getImage();
		return $output;
	}
}