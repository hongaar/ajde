<?php

class AdminUserController extends AdminController
{
    public function view()
    {
        Ajde::app()->getDocument()->setTitle('Users');

        return $this->render();
    }

    public function loginJson()
    {
        $user = new UserModel();

        $id = Ajde::app()->getRequest()->getPostParam('id');

        $return = [false];

        if (false !== $user->loadByPK($id)) {
            $user->login();
            Ajde_Session_Flash::alert(sprintf(trans('Welcome back %s'), $user->getFullname()));
            $return = ['success' => true];
        } else {
            $return = [
                'success' => false,
            ];
        }

        return $return;
    }

    public function resetJson()
    {
        $user = new UserModel();

        $id = Ajde::app()->getRequest()->getPostParam('id');

        $return = [false];

        if (false !== $user->loadByPK($id)) {
            $hash = $user->resetUser();
            $return = [
                'success' => ($hash !== false),
            ];
        } else {
            $return = [
                'success' => false,
            ];
        }

        return $return;
    }
}
