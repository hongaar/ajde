<?php

class Ajde_Crud_Field_Enum extends Ajde_Crud_Field
{
    /**
     * @var int
     */
    private $_chosenTreshold = 15;

    /**
     * @var int
     */
    private $_radioTreshold = 9;

    public function getValues()
    {
        $return = [];
        $options = explode(',', $this->getLength());
        foreach ($options as $option) {
            $option = trim($option, "'");
            if ($this->hasFilter()) {
                if (!in_array($option, $this->getFilter())) {
                    continue;
                }
            }
            if (substr_count($option, ':')) {
                list($key, $value) = explode(':', $option);
            } else {
                $key = $option;
                $value = $option;
            }
            $return[$key] = $value;
        }

        return $return;
    }

    public function useRadio()
    {
        $radioEnabled = count($this->getValues()) < $this->_radioTreshold;
        if ($this->getCrud()->getCollection()->getView()->getMainFilter() == $this->getName()) {
            $radioEnabled = false;
        }
        if ($radioEnabled) {
            $this->_useSpan = false;
        }

        return $radioEnabled;
    }

    public function _getHtmlAttributes()
    {
        $attributes = [];
        if (count($this->getValues()) >= $this->_chosenTreshold) {
            $attributes['class'] = 'chosen';
        }
        if ($this->useRadio()) {
            $attributes['id'] = false;
        }

        return $attributes;
    }
}
