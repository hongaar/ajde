<?php

class MenuCollection extends Ajde_Collection_With_I18n
{
    public function filterByParent($menuId)
    {
        if ($menuId instanceof MenuModel) {
            $menuId = $menuId->getPK();
        }
        $this->addFilter(new Ajde_Filter_Where('parent', Ajde_Filter::FILTER_EQUALS, $menuId));

        return $this;
    }
}
