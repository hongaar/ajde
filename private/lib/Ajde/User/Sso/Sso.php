<?php

abstract class Ajde_User_Sso implements Ajde_User_Sso_Interface
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