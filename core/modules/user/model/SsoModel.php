<?php

class SsoModel extends Ajde_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->setEncryptedFields([
            'data',
        ]);
    }
}
