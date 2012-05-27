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
}
