<?php

class NodetypeModel extends Ajde_Model
{
	protected $_autoloadParents = false;
	protected $_displayField = 'name';

	public function displayIcon()
	{
		return '<span class="badge-icon" title="' . _e($this->displayField()) . '"><i class="'. $this->getIcon() . '"></i></span>';
	}
}
