<?php

class FormController extends Ajde_Controller
{
	public function beforeInvoke()
	{
		return true;
	}

	public function view()
	{
		// get the current form id
		$id = $this->getId();

		$form = new FormModel();
		$form->loadByPK($id);

        // pass form to view
        $this->getView()->assign('form', $form);

        // meta decorator
        $decorator = new Ajde_Crud_Cms_Meta_Decorator();
        $this->getView()->assign('decorator', $decorator);

        // render the temnplate
		return $this->render();
	}
}
