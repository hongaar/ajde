;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Text = function() {
	
	var addWysiwyg = function(elm) {
		$text = $(elm);
		if (typeof $.wysiwyg.rmFormat === 'undefined') {
			alert('Fout bij laden van tekst editor. Vernieuw de pagina a.u.b.');
			return;
		}
		$.wysiwyg.rmFormat.enabled = true;
		$.wysiwyg.removeFormat($text);
		var wysiwyg = $text.wysiwyg({
			css:				'public/css/_core/crud/editor/jwysiwyg/jwysiwyg.editor.css',
			initialContent:		'',
			rmUnusedControls:	true,
			plugins: {
				rmFormat: {
					rmMsWordMarkup: true
				}
			},
			controls:			{					
									bold:			{visible: true},
									italic:			{visible: true},
									underline:		{visible: true},
									strikeThrough:	{visible: true},
									
									subscript:		{visible: true},
									superscript:	{visible: true},
									
									insertOrderedList:		{visible: true},
									insertUnorderedList:	{visible: true},
									
									h1:				{visible: false},
									h2:				{visible: false},
									h3:				{visible: true},
									paragraph:		{visible: true},
									
									createLink:		{visible: true},
									
									insertTable:	{visible: true},
									
									removeFormat:	{visible: true},
									html:			{visible: true}
								}
		});			
	};
	
	return {
		
		init: function() {
			addWysiwyg($('form.ACCrudEdit textarea'));
		}
		
	};
}();