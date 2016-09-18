<?php

abstract class Ajde_Acl_Proxy_Model extends Ajde_Model
{
    public $ignoreAccessControl = false;
    public $autoRedirect = true;

    public function getAclParam()
    {
        return '';
    }

    /**
     * @param string $action
     * @param bool   $autoRedirect
     * @param bool   $determineWildcard
     *
     * @return bool
     */
    public function validateAccess($action, $autoRedirect = true, $determineWildcard = false)
    {
        if ($this->ignoreAccessControl === true) {
            return true;
        }

        $module = (string) $this->getTable();
        $extra = $this->getAclParam();

        $aclTimer = Ajde::app()->addTimer('<i>ACL validation for '.$this->displayField().': '.implode('/',
                ['model', $module, $action, $extra]).'</i>');
        $access = Ajde_Acl::doValidation('model', $module, $action, $extra, [$this, 'validateOwner'],
            [$this, 'validateParent'], $determineWildcard);
        Ajde::app()->endTimer($aclTimer);

        if ($access == false && $this->autoRedirect == true && $autoRedirect == true) {
            $this->validationErrorRedirect();
        }

        return $access;
    }

    private function validationErrorRedirect()
    {
        Ajde_Log::_('ACL firewall hit', Ajde_Log::CHANNEL_SECURITY, Ajde_Log::LEVEL_INFORMATIONAL,
            implode(PHP_EOL, Ajde_Acl::$log));
        Ajde::app()->getRequest()->set('message', trans('You may not have the required permission to view this resource'));
        Ajde::app()->getResponse()->dieOnCode(Ajde_Http_Response::RESPONSE_TYPE_UNAUTHORIZED);
    }

    protected function _load($sql, $values, $populate = true)
    {
        $return = parent::_load($sql, $values, $populate);
        if ($return) {
            $this->validateAccess('read');
        }

        return $return;
    }

    public function insert($pkValue = null, $skipBeforeInsert = false)
    {
        if (method_exists($this, 'beforeInsert')) {
            $this->beforeInsert();
        }
        $this->validateAccess('insert');

        return parent::insert($pkValue, true);
    }

    public function delete()
    {
        $this->validateAccess('delete');

        return parent::delete();
    }

    public function save()
    {
        $this->validateAccess('update');

        return parent::save();
    }

    public function saveMetaValue($metaId, $value)
    {
        if (($this->validateAccess('update', false) || $this->validateAccess('insert', false)) == false) {
            $this->validationErrorRedirect();
        }
        parent::saveMetaValue($metaId, $value);
    }

    public function deleteMetaValue($metaId)
    {
        if (($this->validateAccess('update', false) || $this->validateAccess('insert', false)) == false) {
            $this->validationErrorRedirect();
        }
        parent::deleteMetaValue($metaId);
    }
}
