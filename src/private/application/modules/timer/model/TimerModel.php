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
	
	public function done()
	{
		$this->delete();
	}
	
}
