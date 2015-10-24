<?php

class FormController extends Ajde_Controller
{
	public function beforeInvoke()
	{
		Ajde_Model::register('form');
		return true;
	}

	public function view()
	{
		// get the current form id
		$id = $this->getId();
		
		/* @var $form FormModel */
		$form = $this->getModel();
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