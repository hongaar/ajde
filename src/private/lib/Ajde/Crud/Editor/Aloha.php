<?php

class Ajde_Crud_Editor_Aloha extends Ajde_Crud_Editor
{
	function getResources(Ajde_View &$view) {
		/* @var $view Ajde_Template_Parser_Phtml_Helper */
		
		// Controller
		$view->requireJs('crud/field/text/aloha', 'html', MODULE_DIR . '_core/', Ajde_Document_Format_Html::RESOURCE_POSITION_LAST);
		
		// Library files
		$plugins = array(
			'common/format',
			'common/highlighteditables',
			'common/contenthandler',
			'common/list',
			'common/link',
			'common/table',
			'common/undo',
			'common/paste'
			/*'common/block'*/
		);
		$view->requireJsPublic('_core/crud/editor/aloha/ajde.js', Ajde_Document_Format_Html::RESOURCE_POSITION_LAST);
		$view->requireJsPublic('_core/crud/editor/aloha/lib/aloha.js', Ajde_Document_Format_Html::RESOURCE_POSITION_LAST, 'data-aloha-plugins="' . implode(',', $plugins) . '"');		
		$view->requireCssPublic('_core/crud/editor/aloha/aloha.css');
				
	}
}