<?php

class ReportTodoController extends ReportController
{
	public function active()
	{
		Ajde::app()->getDocument()->setTitle("<span class='page'>Report</span> Active now");
		
		$options = NodeController::getNodeOptions();
		$this->getView()->assign('options', $options);

		return $this->render();
	}		
	
	public function followup()
	{
		Ajde::app()->getDocument()->setTitle("<span class='page'>Report</span> Follow-up");
		
		$options = NodeController::getNodeOptions();
		$this->getView()->assign('options', $options);

		return $this->render();
	}	
	
	public function billable()
	{
		Ajde::app()->getDocument()->setTitle("<span class='page'>Report</span> Billable");
		
		$billableStreaks = array();
		
		$collection = new NodeCollection();
		$collection->orderBy('added');

		// join meta and filter on issue status
		$filterGroup = new Ajde_Filter_WhereGroup();
		$filters = array(
			NodeModel::STREAKSTATUS_APPROVED
		);
		foreach ($filters as $filter) {
			$filterGroup->addFilter(new Ajde_Filter_Where('node_meta.value', Ajde_Filter_Where::FILTER_EQUALS, $filter, Ajde_Query::OP_OR));
		}
		$collection->joinMetaConditional(NodeModel::META_STREAKSTATUS, $filterGroup);
		$collection->getQuery()->addGroupBy('node_meta.node');
		
		foreach($collection as $streak) {
			/* @var $streak NodeModel */
			$children = new NodeCollection();
			$children->filterChildrenOfParent($streak->getPK());
			$children->addFilter(new Ajde_Filter_Where('nodetype', Ajde_Filter_Where::FILTER_EQUALS, NodeModel::NODETYPE_ISSUE));
			$onlyClosed = true;
			foreach($children as $issue) {
				/* @var $issue NodeModel */
				$status = $issue->has('status') ? $issue->get('status') : $issue->getMetaValue('issue_status');
				if ($status !== 'Closed') {
					$onlyClosed = false;
					break;
				}
			}
			if ($onlyClosed) {
				$billableStreaks[] = $streak;
			}
		}
		
		$this->getView()->assign('streaks', $billableStreaks);
		return $this->render();
	}
}