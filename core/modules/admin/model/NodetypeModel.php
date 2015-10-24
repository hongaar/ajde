<?php

class NodetypeModel extends Ajde_Model
{
	protected $_autoloadParents = false;
	protected $_displayField = 'name';

	public function displayIcon()
	{
		return '<span class="badge-icon" title="' . _e($this->displayField()) . '"><i class="'. $this->getIcon() . '"></i></span>';
	}

    public function beforeSave()
    {
        $values = $this->values();
        $required = '';
        foreach($values as $key => $value) {
            if (substr_count('required_', $key)) {
                $required = $key . ',';
            }
        }
        $this->set('required', $required);
    }
}
