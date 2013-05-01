<?php 

class AdminMediaController extends AdminController
{
	private $_extensions = array('mp4', 'avi', 'mp3', 'ogg', 'png', 'jpg', 'jpeg', 'gif', 'pdf', 'xls', 'doc', 'xlsx', 'docx', 'zip');
	private $_imageExtensions = array('jpg', 'jpeg', 'png', 'gif');
	private $_uploaddir = 'public/images/uploads/';
	
	public function view()
	{
		Ajde_Model::register('media');
		
		Ajde::app()->getDocument()->setTitle("Media manager");
        $this->getView()->assign('extensions', $this->_extensions);
		$this->getView()->assign('uploaddir', $this->_uploaddir);
		return $this->render();
	}
    
    public function uploadHtml()
	{		
		Ajde::app()->getDocument()->setTitle("Upload files");
        $this->getView()->assign('extensions', $this->_extensions);
		$this->getView()->assign('uploaddir', $this->_uploaddir);
		return $this->render();
	}
	
	public function uploadButtonHtml()
	{		
        $this->getView()->assign('extensions', $this->_extensions);
		$this->getView()->assign('uploaddir', $this->_uploaddir);
		return $this->render();
	}
	
	public function uploadJson()
	{
		$filename = Ajde::app()->getRequest()->getPostParam('filename');
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$title = pathinfo($filename, PATHINFO_FILENAME);
						
		Ajde_Model::register('media');
		$media = new MediaModel();
		
		$media->name = $title;
		$media->pointer = $filename;
		$media->thumbnail = $filename;
		if (in_array(strtolower($extension), $this->_imageExtensions)) {
			$media->type = 'image';
		} else {
			$media->type = 'file';
		}		
		$media->user = UserModel::getLoggedIn()->getPK();
		
		return array('success' => $media->insert());
	}
}