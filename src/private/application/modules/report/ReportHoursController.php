<?php

class ReportHoursController extends ReportController
{
	public function worked()
	{
		Ajde::app()->getDocument()->setTitle("<span class='page'>Report</span> Work done");
		
		$week = Ajde::app()->getRequest()->getParam('week', date('W'));
		$year = date('Y');
		
		$table = array();
		$totals = array(1 => 0, 2 => 0, 3=> 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
		$weektotal = 0;
		
        $monday = mktime( 0, 0, 0, 1, 1,  $year ) + ((7+1-(date( 'N', mktime( 0, 0, 0, 1, 1,  $year ) )))*86400) + ($week-2)*7*86400 + 1 ;
		
		$collection = new NodeCollection();
		$collection->orderBy('sort');
		
		// filter only roots
		$collection->addFilter(new Ajde_Filter_Where('level', Ajde_Filter::FILTER_EQUALS, 0));

		foreach($collection as $root) {
			/* @var $root NodeModel */

			$columns = array(1 => 0, 2 => 0, 3=> 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
			$issues = array(1 => array(), 2 => array(), 3=> array(), 4 => array(), 5 => array(), 6 => array(), 7 => array());
			$rowtotal = 0;
			
			$children = new NodeCollection();
			
			// filter for current week	
			$mysqlStart = "STR_TO_DATE('" . $year . $week . " Monday', '%x%v %W')";
			$mysqlEnd = "STR_TO_DATE('" . $year . ($week + 1). " Monday', '%x%v %W')";		
			$children->addFilter(new Ajde_Filter_Where('added', Ajde_Filter::FILTER_GREATEROREQUAL, new Ajde_Db_Function($mysqlStart)));
			$children->addFilter(new Ajde_Filter_Where('added', Ajde_Filter::FILTER_LESSOREQUAL, new Ajde_Db_Function($mysqlEnd)));
			
			// filter children
			$children->filterChildrenOfParent($root->getPK());
			
			foreach($children as $child) {
				/* @var $child NodeModel */
				if ($child->getTimeSpent()) {
					// get day
					$date = new DateTime($child->get('added'));
					$date->setTime(0, 0, 0);
					$day = round(($date->format('U') - $monday) / (60*60*24)) + 1;
					$columns[$day] = ($columns[$day] + $child->getTimeSpent());
					$totals[$day] = ($totals[$day] + $child->getTimeSpent());
					$issues[$day][] = array('id' => $child->getPK(), 'title' => $child->getTitle(), 'seconds' => $child->getTimeSpent());
					$rowtotal = $rowtotal + $child->getTimeSpent();
				}
			}
			
			$table[] = array(
				'id' => $root->getPK(),
				'client' => $root->getTitle(),
				'total' => $rowtotal,
				'columns' => $columns,
				'issues' => $issues
			);
			
			$weektotal = $weektotal + $rowtotal;
		}

		$this->getView()->assign('table', $table);
		$this->getView()->assign('totals', $totals);
		$this->getView()->assign('weektotal', $weektotal);
		$this->getView()->assign('monday', $monday);
		$this->getView()->assign('week', $week);
		return $this->render();
	}
	
	public function planning()
	{
		Ajde::app()->getDocument()->setTitle("<span class='page'>Report</span> Planning");
		
		$week = Ajde::app()->getRequest()->getParam('week', date('W'));
		$year = date('Y');
		
		$table = array(1 => array(), 2 => array(), 3=> array(), 4 => array(), 5 => array(), 6 => array(), 7 => array());
		$dayleft = array(1 => 8, 2 => 8, 3=> 8, 4 => 8, 5 => 8, 6 => 8, 7 => 8);
		$totals = array(1 => 0, 2 => 0, 3=> 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
		
        $monday = mktime( 0, 0, 0, 1, 1,  $year ) + ((7+1-(date( 'N', mktime( 0, 0, 0, 1, 1,  $year ) )))*86400) + ($week-2)*7*86400 + 1 ;
		
		$mysqlStart = "STR_TO_DATE('" . $year . $week . " Monday', '%x%v %W')";
		$mysqlEnd = "STR_TO_DATE('" . $year . ($week + 1). " Monday', '%x%v %W')";		
		
		$collection = new NodeCollection();
		$collection->orderBy('sort');
		
		// join meta and filter on issue due
		$filterGroup = new Ajde_Filter_WhereGroup();
		$filterGroup->addFilter(new Ajde_Filter_Where('node_meta.value', Ajde_Filter::FILTER_GREATEROREQUAL, new Ajde_Db_Function($mysqlStart)));
		$filterGroup->addFilter(new Ajde_Filter_Where('node_meta.value', Ajde_Filter::FILTER_LESSOREQUAL, new Ajde_Db_Function($mysqlEnd)));
		$collection->joinMetaConditional(NodeModel::META_ISSUEDUE, $filterGroup);
		$collection->getQuery()->addGroupBy('node_meta.node');
		
		// filter nodetype
		$filterGroup = new Ajde_Filter_WhereGroup();
		$filters = array(
			NodeModel::NODETYPE_ISSUE
		);
		foreach ($filters as $filter) {
			$filterGroup->addFilter(new Ajde_Filter_Where('nodetype', Ajde_Filter_Where::FILTER_EQUALS, $filter, Ajde_Query::OP_OR));
		}
		$collection->addFilter($filterGroup);
		
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

		// add allocated
		$allocatedId = NodeModel::META_ALLOCATED;
		$collection->getQuery()->addSelect(new Ajde_Db_Function(
			"(SELECT node_meta.value FROM node_meta WHERE node_meta.meta = $allocatedId AND node_meta.node = node.id) AS time_allocated"
		));

		foreach($collection as $issue) {
			/* @var $issue NodeModel */
			
			$date = new DateTime($issue->get('due'));
			$date->setTime(0, 0, 0);
			$day = round(($date->format('U') - $monday) / (60*60*24)) + 1;
			
			$hours = max(ceil($issue->getRemaining() / (60*60)), 1);
			if ($issue->getRemaining() === false) {
				$hours = 2;
			} 
			
			$i = 0;
			while($hours > 0 && $i < 999) {
				$i++;
				while ($dayleft[$day] <= 0 && $i < 999) {
					$i++;
					$day--;
				}
				$thisday = min($dayleft[$day], min($hours, 8));
				$table[$day][] = array(
					'id' => $issue->getPK(),
					'title' => $issue->getTitle(),
					'client' => $issue->getRoot()->getTitle(),
					'height' => $thisday,
					'start' => 8 - $dayleft[$day],
					'status' => $issue->get('status')
				);
				$dayleft[$day] = $dayleft[$day] - $thisday;
				$totals[$day] = $totals[$day] + ($thisday*60*60);
				
				$hours = $hours - $thisday;
				$day--;
			}
		}
		
		$this->getView()->assign('table', $table);
		$this->getView()->assign('totals', $totals);
		$this->getView()->assign('monday', $monday);
		$this->getView()->assign('week', $week);
		return $this->render();
	}
}