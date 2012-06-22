;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Text = function() {
	
	var addCKEditor = function(elm) {
		
		elm.ckeditor( function() { /* callback code */ }, {
			skin : 'kama',
			toolbar : 'Ajde',
			format_tags : 'p;h1;h2;h3;pre'
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
			
			addCKEditor($('form.ACCrudEdit textarea'));
		}
		
	};
}();