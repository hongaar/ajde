;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Project === "undefined") {App.Project = function(){}};

App.Project.View = function() {

	var trHandler = function(e) {
		e.stopPropagation();
		var row = $(this);
		detail(row);
	};

	var btnNewProjectHandler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		var row = $(this).parents('tr');
		var id = row.find('input[type=checkbox]').attr('value');
		window.location.href = 'project/?new&view[filter][nodetype]=24&prefill[parent]=' + id;
		return false;
	};

	var btnNodesHandler = function(e) {
		e.stopPropagation();
		var row = $(this).parents('tr');
		detail(row);
	};

	var detail = function(row) {
		var id = row.find('input[type=checkbox]').attr('value');
		window.location.href = 'issue/view?parent=' + id;
	};

	return {

		init: function() {
			$('form.ACCrudList tbody tr').live('click', trHandler);
			$('form.ACCrudList tbody td.buttons a.btn.nodes').live('click', btnNodesHandler);
			$('form.ACCrudList tbody td.buttons a.new-project').live('click', btnNewProjectHandler);
		}

	};
}();

$(document).ready(function() {
	App.Project.View.init();
});