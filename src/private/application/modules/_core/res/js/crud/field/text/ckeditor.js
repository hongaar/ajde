;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Text = function() {

	var addCKEditor = function(elm) {

		// release instances, we need this if pages are loaded with AJAX to
		// make sure no conflict with previous instances occur
		CKEDITOR.instances = {};

		d = elm.ckeditor( function() {
			elm.ckeditorGet().on('change', function() {
				AC.Crud.Edit.setDirty.call(this.element.$);
			});
		}, {
			toolbar : 'Ajde',
			format_tags : 'p;h1;h2;h3;pre',
			width : elm.width() + 13,
			height : elm.height()
		});
	};

	return {

		init: function() {

			CKEDITOR.config.toolbar_Ajde =
			[
				{ name: 'basicstyles',	items : [ 'Format','Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
				{ name: 'paragraph',	items : [ 'NumberedList','BulletedList','-','Outdent','Indent' ] },
				{ name: 'links',		items : [ 'Link','Unlink' ] },
				{ name: 'insert',		items : [ 'Image','Table','SpecialChar' ] },
				{ name: 'tools',		items : [ 'Maximize', 'ShowBlocks','Source' ] }
			];

			CKEDITOR.config.baseHref = document.getElementsByTagName('base')[0].href;
			CKEDITOR.config.forcePasteAsPlainText = true;
            CKEDITOR.config.extraPlugins = 'onchange';

			// Optional configuration
//			CKEDITOR.config.extraPlugins = 'autogrow';
//			CKEDITOR.config.resize_enabled = false;
//			CKEDITOR.config.autoGrow_maxHeight = '600';
//			CKEDITOR.config.removePlugins = 'elementspath,contextmenu';
//			CKEDITOR.config.toolbarCanCollapse = false;

			addCKEditor($('form.ACCrudEdit textarea:not(.noRichText)'));
		}

	};
}();
