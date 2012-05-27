<?php

class SamplesModel extends Ajde_Model
{
	protected $_autoloadParents = true;
	protected $_displayField = 'title';
	
	public function getVATPercentage()
	{
		if (!$this->getVat() instanceof Ajde_Model) {
			$this->loadParents();
		}
		return (float) $this->getVat()->getPercentage() / 100;
	}
}
