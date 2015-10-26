<?php

class AdminUserController extends AdminController
{
	public function view()
	{
		Ajde::app()->getDocument()->setTitle("Users");
		return $this->render();
	}

	public function loginJson()
	{
		$user = new UserModel();

		$id = Ajde::app()->getRequest()->getPostParam('id');

		$return = array(false);

		if (false !== $user->loadByPK($id)) {
			$user->login();
            Ajde_Session_Flash::alert(sprintf(__('Welcome back %s'), $user->getFullname()));
			$return = array('success' => true);
		} else {
			$return = array(
				'success' => false
			);
		}
		return $return;
	}

	public function resetJson()
	{
		$user = new UserModel();

		$id = Ajde::app()->getRequest()->getPostParam('id');

		$return = array(false);

		if (false !== $user->loadByPK($id)) {
			$hash = $user->resetUser();
			$return = array(
				'success' => ( $hash !== false )
			);
		} else {
			$return = array(
				'success' => false
			);
		}
		return $return;
	}
}
