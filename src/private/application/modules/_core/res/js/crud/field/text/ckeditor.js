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
			elm.ckeditorGet().on('focus', function() {
				elm.nextAll('.cke_chrome').addClass('active');
			});
			elm.ckeditorGet().on('blur', function() {
				elm.nextAll('.cke_chrome').removeClass('active');
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
			CKEDITOR.config.contentsCss = [CKEDITOR.basePath + 'contents.css', 'public/css/_core/crud/editor/ckeditor/style.css'];
			CKEDITOR.config.removePlugins = 'elementspath';
			CKEDITOR.config.resize_enabled = false;

			// Optional configuration
//			CKEDITOR.config.extraPlugins = 'autogrow';
//			CKEDITOR.config.resize_enabled = false;
//			CKEDITOR.config.autoGrow_maxHeight = '600';
//			CKEDITOR.config.removePlugins = 'elementspath,contextmenu';
//			CKEDITOR.config.toolbarCanCollapse = false;
		
			setTimeout(function() {
				addCKEditor($('form.ACCrudEdit textarea:not(.noRichText)'));
			}, 100);
		}

	};
}();
