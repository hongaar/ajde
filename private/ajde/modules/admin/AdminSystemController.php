<?php 

class AdminSystemController extends AdminController
{	
	public function check()
	{
		Ajde::app()->getDocument()->setTitle("System check");
		
		$checks = array();
		
		$checks[] = array(
			'msg'	=> 'Directories writable?',
			'fn'	=> 'writable'
		);
		$checks[] = array(
			'msg'	=> 'Production ready?',
			'fn'	=> 'production'
		);
		
		$ret = array();
		
		foreach($checks as $check) {
			$ret = call_user_func(array($this, 'check' . ucfirst($check['fn'])));
			if (empty($ret)) {
				$ret = array(array('msg' => 'OK', 'status' => 'success'));
			}
			foreach($ret as $re) {
				$results[] = array(
					'check'		=> $check['msg'],
					'msg'		=> $re['msg'],
					'status'	=> $re['status']
				);
			}
		}
		
		$this->getView()->assign('results', $results);
		return $this->render();
	}
	
	public function updateHtml()
	{
		Ajde::app()->getDocument()->setTitle("Ajde updater");
		
		$updater = Ajde_Core_Updater::getInstance();
		
		$this->getView()->assign('updater', $updater);		
		return $this->render();
	}
	
	public function updateJson()
	{
		$step = Ajde::app()->getRequest()->getPostParam('step', 'start');
		$status = true;
		if ($step !== 'start') {
			$status = false;
			$updater = Ajde_Core_Updater::getInstance();
			try {
				$status = $updater->update($step);
			} catch(Exception $e) {
				Ajde_Exception_Log::logException($e);
				$status = $e->getMessage();
			}
		}
		return array('status' => $status);
	}
	
	private function checkProduction()
	{
		$files = array(
				'phpinfo.php', 'loadtest.php'
		);
		$ret = array();
		foreach($files as $file) {
			if (file_exists($file)) {
				$ret[] = array(
						'msg'		=> 'File ' . $file . ' should be deleted in production environment',
						'status'	=> 'warning'
				);
			}
		}
		return $ret;
	}
	
	private function checkWritable()
	{
		$dirs = array(
			TMP_DIR, LOG_DIR, CACHE_DIR, UPLOAD_DIR
		);
		$ret = array();
		foreach($dirs as $dir) {
			if (!is_writable($dir)) {
				$ret[] = array(
					'msg'		=> 'Directory ' . $dir . ' is not writable',
					'status'	=> 'important'
				);
			}
		}
		return $ret;
	}
}