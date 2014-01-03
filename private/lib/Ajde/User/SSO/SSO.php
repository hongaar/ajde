<?php

abstract class Ajde_User_SSO
{
    public function getUser()
    {
        $hash = $this->getUidHash();
        $model = new SsoModel();
        if ($hash && $model->loadByField('uid', $hash)) {
            $model->loadParent('user');
            return $model->getUser();
        } else {
            return false;
        }
    }
}