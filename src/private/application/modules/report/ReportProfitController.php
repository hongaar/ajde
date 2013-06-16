<?php

class ReportProfitController extends ReportController
{
	public function weekly()
	{
		Ajde::app()->getDocument()->setTitle("<span class='page'>Report</span> Weekly profit");
		
		$week = Ajde::app()->getRequest()->getParam('week', date('W'));
		$year = date('Y');
		
		$table = array();
		$totals = array(1 => 0, 2 => 0, 3=> 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0);
		$weektotal = 0;
		
		$rate = (int) SettingModel::byName('rate');
		
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
			
			// group to filter issue status, but only for issues
			$filterGroup = new Ajde_Filter_WhereGroup();

			// group to filter status of issues
			$filterAddedGroup = new Ajde_Filter_WhereGroup();
			$filterAddedGroup->addFilter(new Ajde_Filter_Where('added', Ajde_Filter::FILTER_GREATEROREQUAL, new Ajde_Db_Function($mysqlStart)));
			$filterAddedGroup->addFilter(new Ajde_Filter_Where('added', Ajde_Filter::FILTER_LESSOREQUAL, new Ajde_Db_Function($mysqlEnd)));
			$filterAddedGroup->addFilter(new Ajde_Filter_Where('nodetype', Ajde_Filter::FILTER_EQUALSNOT, NodeModel::NODETYPE_STREAK));
			
			$filterGroup->addFilter($filterAddedGroup);
			$filterGroup->addFilter(new Ajde_Filter_Where('nodetype', Ajde_Filter::FILTER_EQUALS, NodeModel::NODETYPE_STREAK, Ajde_Query::OP_OR));
			
			$children->addFilter($filterGroup);
			
			// filter children
			$children->filterChildrenOfParent($root->getPK());
			$children->orderBy('sort');
			
			$streaks = array();
			$lastStreakLevel = 0;
			
			foreach($children as $child) {
				if ($child->get('nodetype') == NodeModel::NODETYPE_STREAK) {
					if ($child->getLevel() <= $lastStreakLevel) {
						$streaks = array();
					}
					$lastStreakLevel = $child->getLevel();
					$streaks[$lastStreakLevel] = $child;
				}
				
				/* @var $child NodeModel */
				if ($child->getTimeSpent()) {
					// get day
					$date = new DateTime($child->get('added'));
					$date->setTime(0, 0, 0);
					$day = round(($date->format('U') - $monday) / (60*60*24)) + 1;
					$time = $child->getTimeSpent();
					
					// find nearest streak
					$level = $child->getLevel();
					$parentStreak = null;
					for($i = ($level - 2); $i > 0; $i--) {
						if (isset($streaks[$i])) {
							$parentStreak = $streaks[$i];
						}
					}
					
					$revenue = 0;
					if (isset($parentStreak)) {
						switch($parentStreak->getBillingType()) {
							case NodeModel::BILLINGTYPE_FIXED:
								$streakCost = $parentStreak->getPriceFixed();
								$streakAllocated = $parentStreak->getTimeAllocated();
								$fraction = $time / $streakAllocated;
								$revenue = $fraction * $streakCost;
								break;
							case NodeModel::BILLINGTYPE_HOURLY:
								$revenue = ($time / 3600) * $rate;
								break;
							case NodeModel::BILLINGTYPE_NOTPAID:
								$revenue = 0;
								break;
						}
						$revenue = round(($revenue - ($revenue * $parentStreak->getDiscount())));
					}
						
					$columns[$day] = ($columns[$day] + $revenue);
					$totals[$day] = ($totals[$day] + $revenue);
					$issues[$day][] = array('id' => $child->getPK(), 'title' => $child->getTitle(), 'revenue' => isset($parentStreak) ? $revenue : 'Could not find streak');
					$rowtotal = $rowtotal + $revenue;
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
}