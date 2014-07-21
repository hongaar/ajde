;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Text = function() {

	var addCKEditor = function(elm) {

		// release instances, we need this if pages are loaded with AJAX to
		// make sure no conflict with previous instances occur
		CKEDITOR.instances = {};

        var triggerChangeTimeout = 1000;
        var changeTimer;

		d = elm.ckeditor( function() {
			this.on('change', function() {
				AC.Crud.Edit.setDirty.call(this.element.$);
//                var element = $(this.element.$);
//                clearTimeout(changeTimer);
//                changeTimer = setTimeout(function() {
//                    element.trigger("change");
//                }, triggerChangeTimeout);
				$(this.element.$).parents('.control-group').removeClass('error');
			});
			this.on('focus', function() {
				$(this.element.$).nextAll('.cke_chrome').addClass('active');
			});
			this.on('blur', function() {
				$(this.element.$).nextAll('.cke_chrome').removeClass('active');
                $(this.element.$).trigger("change");
			});
//			var self = this;
//			$(window).on('resize', function() {
//				self.resize(100);
//				self.resize($(self.element.$).parent().width());
//			});
		}, {
			toolbar		: 'Ajde',
			stylesSet	: 'styles_Ajde',
			format_tags : 'p;h3;pre',
//			width		: elm.width() + 13,
			width		: '100%',
			height		: elm.height(),
			filebrowserImageBrowseUrl : 'admin/media:view.crud?multiple=0&media=1',
		    filebrowserWindowWidth  : 800,
		    filebrowserWindowHeight : 500
		});
	};

	return {

		init: function() {

			CKEDITOR.config.toolbar_Ajde =
			[
				{ name: 'basicstyles',	items : [ 'Styles','Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
				{ name: 'paragraph',	items : [ 'NumberedList','BulletedList','-','JustifyLeft','JustifyCenter','JustifyRight','-','Outdent','Indent' ] },
				{ name: 'links',		items : [ 'Link','Unlink' ] },
				{ name: 'insert',		items : [ 'Image','Table','SpecialChar' ] },
				{ name: 'tools',		items : [ 'Maximize', 'ShowBlocks','Source' ] }
			];
			
			CKEDITOR.stylesSet.add( 'styles_Ajde',
			[
			 	{ name: 'Paragraph', element: 'p' },
			 	{ name: 'Heading', element: 'h3' },
			 	{ name: 'Code', element: 'pre' },
				{ name: 'Default button', element : 'a', attributes : { 'class' : 'btn btn-default' } },
                { name: 'Primary button', element : 'a', attributes : { 'class' : 'btn btn-primary' } },
                { name: 'Red text', element : 'span', attributes : { 'class' : 'red' } },
                { name: 'Gray text', element : 'span', attributes : { 'class' : 'gray' } },
                { name: 'Silver text', element : 'span', attributes : { 'class' : 'silver' } }
			]);

			CKEDITOR.config.resize_enabled = false;

            // Remove all formatting when pasting text copied from websites or Microsoft Word
			CKEDITOR.config.forcePasteAsPlainText = true;
            CKEDITOR.config.pasteFromWordRemoveFontStyles = true;
            CKEDITOR.config.pasteFromWordRemoveStyles = true;
			
			CKEDITOR.config.baseHref = document.getElementsByTagName('base')[0].href;            
			CKEDITOR.config.contentsCss = [CKEDITOR.basePath + 'contents.css', 'public/css/core/ckeditor/style.css'];
			
			CKEDITOR.config.removePlugins = 'elementspath';
			CKEDITOR.config.removeButtons = '';
			
			CKEDITOR.config.extraPlugins = 'onchange,justify,autogrow';
			CKEDITOR.config.extraAllowedContent = 'a(*); img(*); div(*)';

            // autogrow
            CKEDITOR.config.autoGrow_onStartup = true;
            CKEDITOR.config.autoGrow_minHeight = 150;
            CKEDITOR.config.autoGrow_maxHeight = 600;
			
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
