<?php

class Ajde_Crud_Field_Enum extends Ajde_Crud_Field
{
	/**
	 *
	 * @var int 
	 */
    private $_chosenTreshold = 10;
	
	/**
	 *
	 * @var int 
	 */
    private $_radioTreshold = 10;
	
	public function getValues()
	{
		$return = array();
		$options = explode(',', $this->getLength());
		foreach($options as $option) {
			$option = trim($option, "'");
			if ($this->hasFilter()) {
				if (!in_array($option, $this->getFilter())) {
					continue;
				}
			}
			$return[$option] = $option;
		}
		return $return;
	}
	
	public function useRadio()
	{
		$radioEnabled = count($this->getValues()) < $this->_radioTreshold;
		if ($radioEnabled) {
			$this->_useSpan = false;
		}
		return $radioEnabled;
	}
	
	public function _getHtmlAttributes() {
		static $counter;
		$counter++;
        $attributes = array();
        if (count($this->getValues()) >= $this->_chosenTreshold) {
            $attributes['class'] = 'chosen';
        }
		if ($this->useRadio()) {
			$attributes['id'] = 'radio_' . $this->getName() . '_' . $counter;
		}
        return $attributes;
    }
}