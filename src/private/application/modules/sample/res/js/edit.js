;
if (typeof SAMPLES ==="undefined") {SAMPLES = function() {}};
if (typeof SAMPLES.Blog ==="undefined") {SAMPLES.Blog = function() {}};

SAMPLES.Blog.Edit = function() {	
	
	return {
		
		init: function() {
			$('form.ACCrudList td.buttons a.button.view').live('click', SAMPLES.Blog.Edit.viewHandler);			
		},
		
		viewHandler: function(e) {			
			e.stopPropagation();
			e.preventDefault();
			var row = $(this).parents('tr');
			var id = row.find('input[type=checkbox]').attr('value');
			window.location.href = 'samples/view/' + id + '.html';
			return false;
		}
		
	};
}();

$(document).ready(function() {
	SAMPLES.Blog.Edit.init();
});