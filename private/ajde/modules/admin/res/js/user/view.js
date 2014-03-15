;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Admin === "undefined") {App.Admin = function(){}};
if (typeof App.Admin.User === "undefined") {App.Admin.User = function(){}};

App.Admin.User.View = function() {

	var loginHandler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		var row = $(this).parents('tr');
		var form = $(this).parents('form');
		var id = row.find('input[type=checkbox]').attr('value');

		var url = 'admin/user:login.json';
		var data = 'id=' + id;

		// Add CSRF token
		data = data + '&_token=' + form.find('input[name=\'_token\']').val();
		
		$('body').addClass('loading');
		$.post(url, data, function(response) {
			if (response.success === true) {
				AC.Core.Alert.flash('User logged in, please stand by for refresh', 2, function() {
					$('body').addClass('loading');
					window.location.reload(true);
				});
			} else {
				AC.Core.Alert.error('Could not log this user in');
			}
		}, 'json').error(function(jqXHR, message, exception) {
			$('body').removeClass('loading');
			AC.Core.Alert.error(i18n.requestError + ' (' + exception + ')');
		});		
		
		return false;
	};
	
	var resetHandler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		var row = $(this).parents('tr');
		var form = $(this).parents('form');
		var id = row.find('input[type=checkbox]').attr('value');

		var url = 'admin/user:reset.json';
		var data = 'id=' + id;

		// Add CSRF token
		data = data + '&_token=' + form.find('input[name=\'_token\']').val();
		
		$('body').addClass('loading');
		$.post(url, data, function(response) {
			$('body').removeClass('loading');
			if (response.success === true) {
				AC.Core.Alert.flash('User reset link sent');
			} else {
				AC.Core.Alert.error('Could not reset this user');
			}
		}, 'json').error(function(jqXHR, message, exception) {
			$('body').removeClass('loading');
			AC.Core.Alert.error(i18n.requestError + ' (' + exception + ')');
		});		
		
		return false;
	};

	return {

		init: function() {
			$('form.ACCrudList td.buttons a.login').live('click', loginHandler);
			$('form.ACCrudList td.buttons a.reset').live('click', resetHandler);
		}

	};
}();

$(document).ready(function() {
	App.Admin.User.View.init();
});