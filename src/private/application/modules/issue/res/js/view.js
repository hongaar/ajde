;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Issue === "undefined") {App.Issue = function(){}};

App.Issue.View = function() {

	var btnWorkHandler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		var row = $(this).parents('tr');
		var id = row.find('input[type=checkbox]').attr('value');
		App.Timer.Widget.work(id);
		return false;
	};

	return {

		init: function() {			
			$('form.ACCrudList tbody td.buttons a.work').live('click', btnWorkHandler);
		}

	};
}();

$(document).ready(function() {
	App.Issue.View.init();
});