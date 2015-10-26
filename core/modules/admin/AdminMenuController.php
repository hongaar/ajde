<?php

class AdminMenuController extends AdminController
{
	public function view()
	{
		Ajde::app()->getDocument()->setTitle("Menu editor");
		return $this->render();
	}
}
