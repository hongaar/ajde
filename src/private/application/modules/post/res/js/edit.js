if (typeof Application === "undefined") { Application = {}; }
if (typeof Application.Post === "undefined") { Application.Post = {}; }

Application.Post.Edit = function() {
	
	return {
	
		init: function() {
			$('form.ACCrudList td.buttons a.button.view').live('click', Application.Post.Edit.viewHandler);
		},

		viewHandler: function(e) {
			e.stopPropagation();
			e.preventDefault();
			var row = $(this).parents('tr');
			var id = row.find('input[type=checkbox]').attr('value');
			window.location.href = 'post/item/' + id + '.html';
			return false;
		}
		
	};
}();

$(document).ready(function() {
	Application.Post.Edit.init();
});