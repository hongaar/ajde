<?php 

class AdminMediaController extends AdminController
{
	private $_extensions = array('mp4', 'avi', 'swf', 'mp3', 'ogg', 'png', 'jpg', 'jpeg', 'gif', 'pdf', 'xls', 'doc', 'xlsx', 'docx', 'zip');
	private $_imageExtensions = array('jpg', 'jpeg', 'png', 'gif');
	private $_uploaddir = UPLOAD_DIR;
	
	public function view()
	{
		Ajde_Model::register('media');
		
		Ajde::app()->getDocument()->setTitle("Media");
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

    public function driveButtonHtml()
    {
		if (Config::get('driveGoogleKey'))
		{
			$this->getView()->assign('extensions', $this->_extensions);
			$this->getView()->assign('uploaddir', $this->_uploaddir);
			return $this->render();
		}
		return '';
    }
	
	public function uploadJson()
	{
		$filename = Ajde::app()->getRequest()->getPostParam('filename', false, Ajde_Http_Request::TYPE_HTML);

        if (!$filename) {
            return array('success' => false);
        }

		$mediatype = Ajde::app()->getRequest()->getPostParam('mediatype', false);
        $name = Ajde::app()->getRequest()->getPostParam('name', false);
        $oauthToken = Ajde::app()->getRequest()->getPostParam('oauthToken', false);

        Ajde_Model::register('media');
        $media = new MediaModel();

        $media->mediatype = $mediatype;
        $media->pointer = $filename;

        // see if we can save a file from Google Drive
        $driveResult = $media->saveFileFromDrive($name, $oauthToken);
        if ($driveResult === true) {
            $extension = pathinfo($media->pointer, PATHINFO_EXTENSION);
        } else if ($driveResult === 'error') {
            return array('success' => false);
        } else {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $title = pathinfo($filename, PATHINFO_FILENAME);

            $media->name = $title;
            $media->thumbnail = $filename;
        }

		if (in_array(strtolower($extension), $this->_imageExtensions)) {
			$media->type = 'image';
		} else {
			$media->type = 'file';
		}
		$media->user = UserModel::getLoggedIn()->getPK();
		
		return array('success' => $media->insert());
	}
	
	public function typeBtn()
	{
		return $this->render();
	}
	
	public function typeBtnJson()
	{
		Ajde_Model::register('media');
		
		$value = Ajde::app()->getRequest()->getPostParam('type');
		$id = Ajde::app()->getRequest()->getPostParam('id', false);
	
		$model = new MediaModel();
	
		if (!is_array($id)) {
			$id = array($id);
		}
	
		foreach($id as $elm) {
			$model->loadByPK($elm);
			$model->set('mediatype', $value);
			$model->save();
		}
	
		return array(
				'success' => true,
				'message' => Ajde_Component_String::makePlural(count($id), 'media') . ' changed'
		);
	}
}