;
if (typeof App === "undefined") {
    App = function () {
    }
}
if (typeof App.Admin === "undefined") {
    App.Admin = function () {
    }
}
if (typeof App.Admin.Node === "undefined") {
    App.Admin.Node = function () {
    }
}
App.Admin.Node.View = function() {

//	var viewHandler = function(e) {
//		e.stopPropagation();
//		e.preventDefault();
//		var row = $(this).parents('tr');
//		var id = row.find('input[type=checkbox]').attr('value');
//		window.location.href = 'sample/view/' + id + '.html';
//		return false;
//	};
//
	var addChildHandler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		var row = $(this).parents('tr');
		var id = row.find('input[type=checkbox]').attr('value');
		var nodetype = $(this).find('i').data('nodetype');
        nodetype = nodetype || '';
		window.location.href = 'admin/node:view?new&view[filter][nodetype]=' + nodetype + '&prefill[parent]=' + id;
		return false;
	};

	return {

		init: function() {
//			$('form.ACCrudList td.buttons a.btn.view').live('click', viewHandler);
			$('form.ACCrudList td.buttons a.btn.add-child').live('click', addChildHandler);
		}

	};
}();

$(document).ready(function() {
	App.Admin.Node.View.init();
});
