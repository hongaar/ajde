<?php

class BelayController extends AdminController
{
	public function view()
	{
		Ajde::app()->getDocument()->setTitle("What's your issue?");
		return $this->render();
	}
}