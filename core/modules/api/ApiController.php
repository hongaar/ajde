<?php

class ApiController extends Ajde_Api_Controller
{
    public function v1()
    {
        $id    = $this->getId();
        $route = new Ajde_Core_Route('api/v1:' . $id . '.json');

        return Ajde_Controller::fromRoute($route)->invoke();
    }
}
