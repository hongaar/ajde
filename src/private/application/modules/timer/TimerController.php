<?php

class TimerController extends AdminController
{
	protected $_allowedActions = array('widget');
	
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
		
		return array('success' => true, 'issue' => $timer->getNode()->displayField());
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
		$timer->done();
		return array('success' => true);
	}
}