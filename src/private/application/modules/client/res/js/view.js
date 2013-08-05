;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Client === "undefined") {App.Client = function(){}};

App.Client.View = function() {

	var trHandler = function(e) {
		e.stopPropagation();
		var row = $(this);
		detail(row);
	};

//	var btnNodesHandler = function(e) {
//		e.stopPropagation();
//		var row = $(this).parents('tr');
//		detail(row);
//	};

	var detail = function(row) {
		var id = row.find('input[type=checkbox]').attr('value');
		window.location.href = 'project/view?parent=' + id;
	};

	return {

		init: function() {
			$('form.ACCrudList tbody tr').live('click', trHandler);
//			$('form.ACCrudList td.buttons a.button.view').live('click', btnNodesHandler);
		}

	};
}();

$(document).ready(function() {
	App.Client.View.init();
});