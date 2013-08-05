<?php 

class ApiV1Controller extends Ajde_Api_Controller
{
	public function activeissues()
	{
		Ajde_Model::register('node');
		$collection = new NodeCollection();
		
		$collection->orderBy('due');

		// join meta and filter on issue status
		$filterGroup = new Ajde_Filter_WhereGroup();
		$filters = array(
			'Active'
		);
		foreach ($filters as $filter) {
			$filterGroup->addFilter(new Ajde_Filter_Where('node_meta.value', Ajde_Filter_Where::FILTER_EQUALS, $filter, Ajde_Query::OP_OR));
		}
		$collection->joinMetaConditional(NodeModel::META_ISSUESTATUS, $filterGroup);
		$collection->getQuery()->addGroupBy('node_meta.node');

		// add issue status and filter
		$issuestatusId = NodeModel::META_ISSUESTATUS;
		$collection->getQuery()->addSelect(new Ajde_Db_Function(
			"(SELECT node_meta.value FROM node_meta WHERE node_meta.meta = $issuestatusId AND node_meta.node = node.id) AS status"
		));

		// add issue due
		$issuedueId = NodeModel::META_ISSUEDUE;
		$collection->getQuery()->addSelect(new Ajde_Db_Function(
			"(SELECT node_meta.value FROM node_meta WHERE node_meta.meta = $issuedueId AND node_meta.node = node.id) AS due"
		));		
		
		// add issue due
		$issuedueId = NodeModel::META_ISSUEDUE;
		$collection->getQuery()->addSelect(new Ajde_Db_Function(
			"(SELECT DATEDIFF(node_meta.value, '" . date('Y-m-d') . "') FROM node_meta WHERE node_meta.meta = $issuedueId AND node_meta.node = node.id) AS duedays"
		));		

		// add allocated
		$allocatedId = NodeModel::META_ALLOCATED;
		$collection->getQuery()->addSelect(new Ajde_Db_Function(
			"(SELECT node_meta.value FROM node_meta WHERE node_meta.meta = $allocatedId AND node_meta.node = node.id) AS time_allocated"
		));

		// add node type
		$collection->joinNodetype();
		$collection->getQuery()->addSelect('nodetype.name AS nodetype_name');

		// filter nodetype
		$filterGroup = new Ajde_Filter_WhereGroup();
		$filters = array(
			NodeModel::NODETYPE_ISSUE
		);
		foreach ($filters as $filter) {
			$filterGroup->addFilter(new Ajde_Filter_Where('nodetype', Ajde_Filter_Where::FILTER_EQUALS, $filter, Ajde_Query::OP_OR));
		}
		$collection->addFilter($filterGroup);
		
		foreach($collection as $item) {
			/* @var $item NodeModel */
			$item->setClient('');
			$root = $item->getRoot();
			if ($root) {		
				$item->setClient($root->getTitle());
			}
		}
		
		return (object) array('data' => $collection->toArray());
	}
	
	public function timer()
	{
		Ajde_Model::register('timer');
		Ajde_Model::register('node');
		
		$timer = TimerModel::getCurrentTimer();
		
		if ($timer) {
			$values = $timer->values();
			$values['title'] = $timer->getNode()->getTitle();
			if (!$timer->isPaused()) {
				$values['elapsed'] = $timer->getTotalSeconds();
			}						
		} else {
			$values = false;
		}
		
		return (object) array('data' => $values);
	}
	
	public function work()
	{
		Ajde_Model::register('timer');
		Ajde_Model::register('node');
		
		if (TimerModel::getCurrentTimer()) {
			return (object) array('success' => false, 'message' => 'Another timer active');
		}
		
		$node = (int) Ajde::app()->getRequest()->getParam('node', false);
		$timer = new TimerModel();
		$timer->populate(array(
			'node' => $node,
			'user' => UserModel::getLoggedIn()->getPK()
		));
		$timer->insert();
		$timer->work();
		
		return (object) array('success' => true, 'issue' => $timer->getNode()->getPK(), 'display' => $timer->getNode()->displayField());
	}
	
	public function pause()
	{
		Ajde_Model::register('timer');
		Ajde_Model::register('node');
		
		if (!$timer = TimerModel::getCurrentTimer()) {
			return (object) array('success' => false, 'message' => 'No active timer');
		}
		$timer->pause();
		return (object) array('success' => true);
	}
	
	public function resume()
	{
		Ajde_Model::register('timer');
		Ajde_Model::register('node');
		
		if (!$timer = TimerModel::getCurrentTimer()) {
			return (object) array('success' => false, 'message' => 'No paused timer');
		}
		$timer->resume();
		return (object) array('success' => true);
	}
	
	public function done()
	{
		Ajde_Model::register('timer');
		Ajde_Model::register('node');
		
		if (!$timer = TimerModel::getCurrentTimer()) {
			return (object) array('success' => false, 'message' => 'No active timer');
		}
		
		$description = Ajde::app()->getRequest()->getParam('description', 'Untitled worklog');
		$seconds = Ajde::app()->getRequest()->getParam('seconds', false);
		$status = Ajde::app()->getRequest()->getParam('status', false);
		
		if (!$description) { $description = 'Untitled worklog'; }
		if (empty($status)) { $status = false; }
		
		$timer->done($description, $seconds, $status);
		
		return (object) array('success' => true);
	}
	
	public function cancel()
	{
		Ajde_Model::register('timer');
		Ajde_Model::register('node');
		
		if (!$timer = TimerModel::getCurrentTimer()) {
			return (object) array('success' => false, 'message' => 'No active timer');
		}
		$timer->cancel();
		return (object) array('success' => true);
	}
}
