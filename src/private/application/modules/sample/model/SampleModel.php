<?php

class SampleModel extends Ajde_Model
{
	protected $_autoloadParents = true;
	protected $_displayField = 'title';
	
	public function getVATPercentage()
	{
		if ($this->hasVat() && !$this->getVat() instanceof Ajde_Model) {
			$this->loadParents();
		}
		return $this->hasVat() ? ((float) $this->getVat()->getPercentage() / 100) : 0;
	}
}
