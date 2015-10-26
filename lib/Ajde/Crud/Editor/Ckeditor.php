<?php

class Ajde_Crud_Editor_Ckeditor extends Ajde_Crud_Editor
{
    function getResources(&$view)
    {
        /* @var $view Ajde_Template_Parser_Phtml_Helper */

        // Library files
        $view->requireJsPublic('core/ckeditor/lib/ckeditor.js');
        $view->requireJsPublic('core/ckeditor/lib/adapters/jquery.js');

        // Controller
        $view->requireJs('crud/field/text/ckeditor', 'html', MODULE_DIR . '_core/',
            Ajde_Document_Format_Html::RESOURCE_POSITION_LAST);
    }
}
