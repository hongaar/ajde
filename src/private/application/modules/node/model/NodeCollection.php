<?php

class NodeCollection extends Ajde_Collection
{
	public function filterByType($type)
	{
		$niceName = str_replace('_', ' ', $type);
		$nodetype = new NodetypeModel();
		if ($nodetype->loadByField('name', $niceName)) {
			$this->addFilter(new Ajde_Filter_Where('nodetype', Ajde_Filter::FILTER_EQUALS, $nodetype->getPK()));
		}		
		return $this;
	}
	
	public function filterByParent($nodeId)
	{
		if ($nodeId instanceof NodeModel) {
			$nodeId = $nodeId->getPK();
		}
		$this->addFilter(new Ajde_Filter_Where('parent', Ajde_Filter::FILTER_EQUALS, $nodeId));
		return $this;
	}
}
