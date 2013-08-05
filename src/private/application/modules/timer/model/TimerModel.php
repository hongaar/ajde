<?php

class TimerModel extends Ajde_Model
{
	protected $_autoloadParents = false;
	protected $_hasMeta = false;
	
	/**
	 * 
	 * @return TimerModel
	 */
	public static function getCurrentTimer()
	{
		$user = UserModel::getLoggedIn();
		$timer = new self();
		$timer->loadByField('user', (string) $user);
		return $timer->hasLoaded() ? $timer : false;
	}
	
	/**
	 * 
	 * @return NodeModel
	 */
	public function getNode()
	{
		Ajde_Model::register('node');
		$node = new NodeModel();
		$node->loadByPK($this->get('node'));
		return $node;
	}
	
	public function isPaused()
	{
		return $this->get('status') == 'paused';
	}
	
	public function getSecondsSinceLastUpdated()
	{
		$started = strtotime($this->get('updated'));
		return time() - $started;
	}
	
	public function getTotalSeconds()
	{
		$elapsed = $this->get('elapsed') * 1;
		$sinceLastUpdated = $this->getSecondsSinceLastUpdated() * 1;
		$totalElapsed = $elapsed + $sinceLastUpdated;
		return $totalElapsed;
	}
	
	public function getElapsed()
	{
		if ($this->isPaused()) {
			return $this->get('elapsed');
		} else {
			return $this->getTotalSeconds();
		}
	}
	
	public function work()
	{
		Ajde_Model::register('node');
				
		// make new worklog
		$issue = $this->getNode();
		$issue->saveMetaValue(NodeModel::META_ISSUESTATUS, NodeModel::ISSUESTATUS_ACTIVE);
	}
	
	public function pause()
	{
		$this->set('status', 'paused');
		$newElapsed = $this->getTotalSeconds();
		$this->set('elapsed', $newElapsed);
		$this->save();
	}
	
	public function resume()
	{
		$this->set('status', 'active');
		$this->save();
	}
	
	public function done($description, $seconds = false, $status = false)
	{
		Ajde_Model::register('node');
		
		if ($seconds === false) {
			$seconds = $this->getTotalSeconds();		
		}
		
		// make new worklog
		$log = new NodeModel();
		$log->setNodetype(NodeModel::NODETYPE_WORK);
		$log->setParent($this->get('node'));
		$log->setSummary($description);
		$log->setTitle(Ajde_Component_String::trim($description, 30));
		$log->setUser(UserModel::getLoggedIn()->getPK());
		$log->insert();
		$log->saveMetaValue('time_spent', $seconds);
		
		$log->sortTree('NodeCollection');
		$log->updateTimeSpent();
		$log->updateTotalCost();
		
		if ($status) {
			$this->getNode()->saveMetaValue(NodeModel::META_ISSUESTATUS, $status);
		}
		
		$this->delete();
	}
	
	public function cancel()
	{
		Ajde_Model::register('node');		
		$this->delete();
	}
	
}
