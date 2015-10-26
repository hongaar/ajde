<?php

class ApiV1Controller extends Ajde_Api_Controller
{
	public function nodes()
	{
		$collection = new NodeCollection();

		$collection->orderBy('updated');

		// add node type
		$collection->joinNodetype();
		$collection->getQuery()->addSelect('nodetype.name AS nodetype_name');

		return (object) array('data' => $collection->toArray());
	}
}
