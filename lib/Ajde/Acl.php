<?php

class Ajde_Acl extends Ajde_Model
{
    public static $log = [];
    public static $access = null;

    protected $_autoloadParents = false;

    private static $_aclCollectionCache = [];
    private static $_aclRulesCache = [];

    private static $_user;

    public static function getLog()
    {
        return self::$log;
    }

    private static function getUser()
    {
        if (!isset(self::$_user)) {
            self::$_user = Ajde_User::getLoggedIn();
        }

        return self::$_user;
    }

    private static function getUserId()
    {
        $user = self::getUser();
        if ($user !== false) {
            return $user->getPK();
        }

        return -1;
    }

    private static function getUsergroupId()
    {
        $user = self::getUser();
        if ($user !== false) {
            return (string)$user->getUsergroup();
        }

        return -1;
    }

    /**
     * @return AclCollection
     */
    private static function getAclCollection()
    {
        return new AclCollection();
    }

    /**
     * @return AclCollection
     */
    private static function getAclModel()
    {
        return new AclModel();
    }

    /**
     *
     * @return Ajde_Acl|boolean
     */
    public static function lookup($usergroup, $entity, $module, $action = '*', $extra = '*', $permission = false)
    {
        $acl = self::getAclModel();
        $type = ($usergroup === 'public' ? 'public' : 'usergroup');
        $fields = [
            'entity' => $entity,
            'type' => $type,
            'module' => $module,
            'action' => $action,
            'extra' => $extra
        ];
        if (is_numeric($usergroup)) {
            $fields['usergroup'] = $usergroup;
        }
        if ($permission !== false) {
            $fields['permission'] = $permission;
        }
        if ($acl->loadByFields($fields)) {
            return $acl;
        }

        return false;
    }

    public static function removePermission(
        $usergroup,
        $entity,
        $module,
        $action = '*',
        $extra = '*',
        $permission = false
    ) {
        $acl = self::lookup($usergroup, $entity, $module, $action, $extra, $permission);
        if ($acl) {
            return $acl->delete();
        }

        return false;
    }

    public static function removeModelPermissions($usergroup, $model, $extra = '*')
    {
        $collection = self::getModelActions($usergroup, $model, $extra);
        $success = true;
        foreach ($collection as $acl) {
            $success = $success * $acl->delete();
        }

        return $success == true;
    }

    public static function addPermission($permission, $entity, $usergroup, $module, $action = '*', $extra = '*')
    {
        $acl = self::getAclModel();
        $type = ($usergroup === 'public' ? 'public' : 'usergroup');
        $values = [
            'entity' => $entity,
            'type' => $type,
            'module' => $module,
            'action' => $action,
            'extra' => $extra,
            'permission' => $permission
        ];
        if (is_numeric($usergroup)) {
            $values['usergroup'] = $usergroup;
        }
        $acl->populate($values);

        return $acl->insert();
    }

    /**
     *
     * @return AclCollection
     */
    public static function getModelActions($usergroup, $model, $extra = '*', $permission = false)
    {
        $collection = self::getAclCollection();
        $collection->addFilter(new Ajde_Filter_Where('entity', Ajde_Filter::FILTER_EQUALS, 'model'));
        $type = ($usergroup === 'public' ? 'public' : 'usergroup');
        if (is_numeric($usergroup)) {
            $collection->addFilter(new Ajde_Filter_Where('usergroup', Ajde_Filter::FILTER_EQUALS, $usergroup));
        }
        $collection->addFilter(new Ajde_Filter_Where('type', Ajde_Filter::FILTER_EQUALS, $type));
        $collection->addFilter(new Ajde_Filter_Where('module', Ajde_Filter::FILTER_EQUALS, $model));
        if ($permission !== false) {
            $collection->addFilter(new Ajde_Filter_Where('permission', Ajde_Filter::FILTER_EQUALS, $permission));
        }
        $collection->addFilter(new Ajde_Filter_Where('extra', Ajde_Filter::FILTER_EQUALS, $extra));

        return $collection;
    }

    public static function getModelActionsAsArray($usergroup, $model, $extra = '*', $permission = false)
    {
        $collection = self::getModelActions($usergroup, $model, $extra, $permission);
        $actions = [];
        foreach ($collection as $acl) {
            $actions[] = $acl->getAction();
        }

        return $actions;
    }

    public static function getPagePermission($usergroup, $module, $action = '*', $extra = '*')
    {
        $acl = self::lookup($usergroup, 'page', $module, $action, $extra);
        if ($acl) {
            return $acl->getPermission();
        }

        return false;
    }

    public static function validateController($module, $action, $extra)
    {
        $access = self::validatePage($module, $action, $extra);
        Ajde_Acl::$access = $access;

        return $access;
    }

    public static function validatePage($module, $action, $extra)
    {
        return self::doValidation('page', $module, $action, $extra);
    }

    private static function validateOwner($uid, $gid)
    {
        return false;
    }

    private static function validateParent($uid, $gid)
    {
        return false;
    }

    /**
     * @param string $entity
     * @param string $module
     * @param string $action
     * @param string $extra
     * @param bool $ownerCallback
     * @param bool $parentCallback
     * @param bool $determineWildcard
     * @return bool
     */
    public static function doValidation(
        $entity,
        $module,
        $action,
        $extra,
        $ownerCallback = false,
        $parentCallback = false,
        $determineWildcard = false
    ) {
        $uid = self::getUserId();
        $usergroup = self::getUsergroupId();

        $isWildcard = false;

        $callbackHash = '';
        if ($ownerCallback !== false && $parentCallback !== false) {
            $callbackHash = md5(get_class($ownerCallback[0]) . get_class($parentCallback[0]) . $ownerCallback[1] . $parentCallback[1]);
        }
        $validationHash = md5($entity . '/' . $module . '/' . $action . '/' . $extra . '/' . $uid . '/' . $usergroup . '/' . $callbackHash);

        if (isset(self::$_aclRulesCache[$validationHash])) {
            $orderedRules = self::$_aclRulesCache[$validationHash];
        } else {

            /**
             * Allright, this is how things go down here:
             * We want to check for at least one allowed or owner record in this direction:
             *
             * 1. Wildcard usergroup AND module/action
             * 2. Wildcard user AND module/action
             * 3. Specific usergroup AND module/action
             * 4. Specific user AND module/action
             * 5. Public AND module/action
             *
             * Module/action goes down in this order:
             *
             * A1. Wildcard module AND wildcard action
             * A2. Wildcard module AND wildcard action (with extra)
             * B1. Wildcard module AND specific action
             * B2. Wildcard module AND specific action (with extra)
             * C1. Specific module AND wildcard action
             * C2. Specific module AND wildcard action (with extra)
             * D1. Specific module AND specific action
             * D2. Specific module AND specific action (with extra)
             *
             * This makes for 20 checks.
             *
             * If a denied record is found and no allowed or owner record is present
             * further down, deny access.
             */

            $access = null;

            $moduleAction = [
                "A1" => [
                    'module' => '*',
                    'action' => '*',
                    'extra' => '*'
                ],
                "A2" => [
                    'module' => '*',
                    'action' => '*',
                    'extra' => $extra
                ],
                "B1" => [
                    'module' => '*',
                    'action' => $action,
                    'extra' => '*'
                ],
                "B2" => [
                    'module' => '*',
                    'action' => $action,
                    'extra' => $extra
                ],
                "C1" => [
                    'module' => $module,
                    'action' => '*',
                    'extra' => '*'
                ],
                "C2" => [
                    'module' => $module,
                    'action' => '*',
                    'extra' => $extra
                ],
                "D1" => [
                    'module' => $module,
                    'action' => $action,
                    'extra' => '*'
                ],
                "D2" => [
                    'module' => $module,
                    'action' => $action,
                    'extra' => $extra
                ]
            ];

            $userGroup = [
                1 => ['usergroup', null],
                2 => ['user', null],
                3 => ['usergroup', $usergroup],
                4 => ['user', $uid],
                5 => ['public', null]
            ];

            /**
             * Allright, let's prepare the SQL!
             */

            // From cache
            if (isset(self::$_aclCollectionCache[$entity])) {

                $rules = self::$_aclCollectionCache[$entity];
                // Load collection
            } else {

                $rules = self::getAclCollection();
                $rules->reset();

                //		$moduleActionWhereGroup = new Ajde_Filter_WhereGroup(Ajde_Query::OP_AND);
                //		foreach($moduleAction as $moduleActionPart) {
                //			$group = new Ajde_Filter_WhereGroup(Ajde_Query::OP_OR);
                //			foreach($moduleActionPart as $key => $value) {
                //				$group->addFilter(new Ajde_Filter_Where($key, Ajde_Filter::FILTER_EQUALS, $value, Ajde_Query::OP_AND));
                //			}
                //			$moduleActionWhereGroup->addFilter($group);
                //		}
                //
                //		foreach($userGroup as $userGroupPart) {
                //			$group = new Ajde_Filter_WhereGroup(Ajde_Query::OP_OR);
                //			$comparison = is_null($userGroupPart[1]) ? Ajde_Filter::FILTER_IS : Ajde_Filter::FILTER_EQUALS;
                //			$group->addFilter(new Ajde_Filter_Where('type', Ajde_Filter::FILTER_EQUALS, $userGroupPart[0], Ajde_Query::OP_AND));
                //			if ($userGroupPart[0] !== 'public') {
                //				$group->addFilter(new Ajde_Filter_Where($userGroupPart[0], $comparison, $userGroupPart[1], Ajde_Query::OP_AND));
                //			}
                //			$group->addFilter($moduleActionWhereGroup, Ajde_Query::OP_AND);
                //			$rules->addFilter($group, Ajde_Query::OP_OR);
                //		}

                // add the entity filter
                $rules->filterByEntity($entity);

                // do the load
                $rules->load();

                self::$_aclCollectionCache[$entity] = $rules;
            }

            /**
             * Oempfff... now let's traverse and set the order
             *
             * Update: It seems that we can just load the entire ACL table in the collection
             * and use this traversal to find matching rules instead of executing this
             * overly complicated SQL query constructed above...
             */

            $orderedRules = [];
            foreach ($userGroup as $ugpKey => $userGroupPart) {
                $type = $userGroupPart[0];
                $ugId = $userGroupPart[1];
                foreach ($moduleAction as $maKey => $moduleActionPart) {
                    $module = $moduleActionPart['module'];
                    $action = $moduleActionPart['action'];
                    $extra = $moduleActionPart['extra'];
                    $rule = $rules->findRule($type, $ugId, $module, $action, $extra);
                    if ($rule !== false) {
                        $orderedRules[$ugpKey . $maKey] = $rule;
                    }
                }
            }

            self::$_aclRulesCache[$validationHash] = $orderedRules;
        }

        /**
         * Finally, determine access
         */
        $extra = ($extra !== '*' && $extra !== '') ? ' (' . $extra . ')' : '';
        foreach ($orderedRules as $key => $rule) {
            if ($rule->type === 'public' && self::getUser() === false) {
                switch ($rule->permission) {
                    case "allow":
                        Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' allows access for ' . $module . '/' . $action . $extra . ' (public)';
                        $access = true;
                        $isWildcard = $rule->extra == '*';
                        break 2;
                    case "deny":
                    default:
                        Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' denies access for ' . $module . '/' . $action . $extra . ' (public)';
                        $access = false;
                        break;
                }
            } else {
                if ($rule->type !== 'public') {
                    if (self::getUser()) {
                        switch ($rule->permission) {
                            case "deny":
                                Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' denies access for ' . $module . '/' . $action . $extra;
                                $access = false;
                                break;
                            case "own":
                                if (call_user_func_array($ownerCallback, [$uid, $usergroup])) {
                                    Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' allows access for ' . $module . '/' . $action . $extra . ' (owner)';
                                    $access = true;
                                    $isWildcard = $rule->extra == '*';
                                    break 2;
                                } else {
                                    Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' denies access for ' . $module . '/' . $action . $extra . ' (owner)';
                                    // TODO: or inherit?
                                    $access = false;
                                }
                                break;
                            case "parent":
                                if (call_user_func_array($parentCallback, [$uid, $usergroup])) {
                                    Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' allows access for ' . $module . '/' . $action . $extra . ' (parent)';
                                    $access = true;
                                    $isWildcard = $rule->extra == '*';
                                    break 2;
                                } else {
                                    Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' denies access for ' . $module . '/' . $action . $extra . ' (parent)';
                                    // TODO: or inherit?
                                    $access = false;
                                }
                                break;
                            case "allow":
                                Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' allows access for ' . $module . '/' . $action . $extra;
                                $access = true;
                                $isWildcard = $rule->extra == '*';
                                break 2;
                        }
                    } else {
                        Ajde_Acl::$log[] = $key . ' match with ACL rule id ' . $rule->getPK() . ' denies access for ' . $module . '/' . $action . $extra . ' (not logged in)';
                        $access = false;
                    }
                }
            }
        }

        if (!isset($access)) {
            Ajde_Acl::$log[] = 'No match in ACL rules denies access for ' . $module . '/' . $action . $extra;
            $access = false;
        }

        if ($determineWildcard) {
            return $isWildcard;
        }

        return $access;
    }
}
