;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Admin === "undefined") {App.Admin = function(){}};
if (typeof App.Admin.Setup === "undefined") {App.Admin.Setup = function(){}};

App.Admin.Setup.Types = function() {

	var metaHandler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		var row = $(this).parents('tr');
		var id = row.find('input[type=checkbox]').attr('value');
		window.location.href = 'admin/setup:meta?view[filter][nodetype]=' + id;
		return false;
	};

	return {

		init: function() {
			$('form.ACCrudList td.buttons a.btn.meta').live('click', metaHandler);
		}

	};
}();

$(document).ready(function() {
	App.Admin.Setup.Types.init();
});