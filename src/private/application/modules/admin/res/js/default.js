if (typeof Application === "undefined") {
	Application = {};
}

Application.Admin = function() {
	
	return {
	
		init: function() {
			$('header').find('ul').remove();
			$admin = $('<a href=\'admin\' class=\'action\'>ADMIN HOME</a>');
			$site = $('<a href=\'.\' class=\'action\' target=\'_test\'>SITE</a>');
			$('header').find('nav').append($admin).append($site);

			$('form.ACCrudList td.buttons a.button.view').live('click', Application.Admin.viewHandler);
		},

		viewHandler: function(e) {
			e.stopPropagation();
			e.preventDefault();
			var row = $(this).parents('tr');
			var id = row.find('input[type=checkbox]').attr('value');
			window.location.href = 'portfolio/item/' + id + '.html';
			return false;
		}
		
	};
}();

$(document).ready(function() {
	Application.Admin.init();
});