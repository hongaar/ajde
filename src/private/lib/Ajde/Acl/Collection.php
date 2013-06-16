<?php

class Ajde_Acl_Collection extends Ajde_Collection
{
	public function findRule($type, $ugId, $module, $action, $extra)
	{
		foreach($this as $rule)
		{
			if (
					(
						($rule->get('type') === 'public' && $type === 'public') ||
						($rule->get('type') === $type && $rule->get($type) === $ugId)
					)
					&& $rule->get('module') === $module
					&& $rule->get('action') === $action
					&& $rule->get('extra') === $extra
				) {
				return $rule;
			}
		}
		return false;
	}
	
	/**
	 * 
	 * @param string $entity
	 * @return Ajde_Acl_Collection
	 */
	public function filterByEntity($entity) {
		return $this->addFilter(new Ajde_Filter_Where('entity', Ajde_Filter::FILTER_EQUALS, $entity));
	}
}
