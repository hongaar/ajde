<?php

class TimerController extends AdminController
{
	protected $_allowedActions = array('widget', 'modal');
	
	public function beforeInvoke() {
		Ajde_Model::register($this);
		return parent::beforeInvoke($this->_allowedActions);		
	}
	
	public function widget()
	{	
		$timer = TimerModel::getCurrentTimer();
		$this->getView()->assign('timer', $timer);
		return $this->render();
	}
	
	public function modal()
	{	
		return $this->render();
	}
	
	public function workJson()
	{
		if (TimerModel::getCurrentTimer()) {
			return array('success' => false, 'message' => 'Another timer active');
		}
		
		$node = (int) Ajde::app()->getRequest()->getPostParam('node', false);
		$timer = new TimerModel();
		$timer->populate(array(
			'node' => $node,
			'user' => UserModel::getLoggedIn()->getPK()
		));
		$timer->insert();
		$timer->work();
		
		return array('success' => true, 'issue' => $timer->getNode()->getPK(), 'display' => $timer->getNode()->displayField());
	}
	
	public function pauseJson()
	{
		if (!$timer = TimerModel::getCurrentTimer()) {
			return array('success' => false, 'message' => 'No active timer');
		}
		$timer->pause();
		return array('success' => true);
	}
	
	public function resumeJson()
	{
		if (!$timer = TimerModel::getCurrentTimer()) {
			return array('success' => false, 'message' => 'No paused timer');
		}
		$timer->resume();
		return array('success' => true);
	}
	
	public function doneJson()
	{
		if (!$timer = TimerModel::getCurrentTimer()) {
			return array('success' => false, 'message' => 'No active timer');
		}
		
		$description = Ajde::app()->getRequest()->getPostParam('description', 'Untitled worklog');
		$seconds = Ajde::app()->getRequest()->getPostParam('seconds', false);
		$status = Ajde::app()->getRequest()->getPostParam('status', false);
		
		if (!$description) { $description = 'Untitled worklog'; }
		if (empty($status)) { $status = false; }
		
		$timer->done($description, $seconds, $status);
		
		return array('success' => true);
	}
	
	public function cancelJson()
	{
		if (!$timer = TimerModel::getCurrentTimer()) {
			return array('success' => false, 'message' => 'No active timer');
		}
		$timer->cancel();
		return array('success' => true);
	}
}