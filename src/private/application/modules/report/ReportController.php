<?php

class ReportController extends AdminController
{
	public function beforeInvoke($allowed = array())
	{
		Ajde_Model::register('admin');
		Ajde_Model::register('node');
		Ajde_Model::register('media');
		Ajde_Model::register('tag');
		return parent::beforeInvoke($allowed);
	}
}