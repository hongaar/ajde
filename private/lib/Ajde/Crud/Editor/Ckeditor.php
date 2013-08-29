<?php

class Ajde_Crud_Editor_Ckeditor extends Ajde_Crud_Editor
{
	function getResources(&$view) {
		/* @var $view Ajde_Template_Parser_Phtml_Helper */
		
		// Installed?
		if (!file_exists(PUBLIC_DIR . 'js/_core/crud/editor/ckeditor/lib/ckeditor.js')) {
			// @todo
			throw new Ajde_Exception('CKEditor not installed, see <a href="https://github.com/hongaar/ajde/tree/master/src/public/js/_core/crud/editor/ckeditor">README</a>');
		}
		
		// Library files
		$view->requireJsPublic('_core/crud/editor/ckeditor/lib/ckeditor.js');
		$view->requireJsPublic('_core/crud/editor/ckeditor/lib/adapters/jquery.js');
		
		// Controller
		$view->requireJs('crud/field/text/ckeditor', 'html', MODULE_DIR . '_core/', Ajde_Document_Format_Html::RESOURCE_POSITION_LAST);
	}
}