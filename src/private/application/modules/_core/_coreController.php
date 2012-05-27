<?php

class _coreController extends Ajde_Controller
{
	public function view()
	{
		Ajde::app()->getResponse()->redirectNotFound();
	}
}