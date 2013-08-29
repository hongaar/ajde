;
if (typeof Application === "undefined") {Application = function(){}};
if (typeof Application.Sample === "undefined") {Application.Sample = function() {}};

Application.Sample.Edit = function() {

	return {

		init: function() {
			$('form.ACCrudList td.buttons a.button.view').live('click', Application.Sample.Edit.viewHandler);
		},

		viewHandler: function(e) {
			e.stopPropagation();
			e.preventDefault();
			var row = $(this).parents('tr');
			var id = row.find('input[type=checkbox]').attr('value');
			window.location.href = 'sample/view/' + id + '.html';
			return false;
		}

	};
}();

$(document).ready(function() {
	Application.Sample.Edit.init();
});