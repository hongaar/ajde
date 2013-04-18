;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Admin === "undefined") {App.Admin = function(){}};
if (typeof App.Admin.Node === "undefined") {App.Admin.Node = function(){}};

App.Admin.Node.View = function() {

	var viewHandler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		var row = $(this).parents('tr');
		var id = row.find('input[type=checkbox]').attr('value');
		window.location.href = 'sample/view/' + id + '.html';
		return false;
	};

	return {

		init: function() {
			$('form.ACCrudList td.buttons a.btn.view').live('click', viewHandler);
		}

	};
}();

$(document).ready(function() {
	App.Admin.Node.View.init();
});