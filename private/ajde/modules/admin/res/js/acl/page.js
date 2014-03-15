;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Admin === "undefined") {App.Admin = function(){}};
if (typeof App.Admin.Acl === "undefined") {App.Admin.Acl = function(){}};

App.Admin.Acl.Page = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	return {

		init: function() {
			$('#AclPage')
				.bind('result', this.resultHandler)
				.bind('error', this.errorHandler);
		
			$('button.toggle').on('click', this.toggleHandler);
			$('input.toggle').each(this.setButtonState);
		},
			
		toggleHandler: function(e) {
			$(this).parents('div.controls').find('input:first').val($(this).data('value'));
		},
			
		setButtonState: function() {
			$(this).nextAll('div.btn-group:first').find('button[data-value=' + $(this).val() + ']').addClass('active');
		},
			
		resultHandler: function(event, data) {
			if (data.success === false) {
				errorHandler(data.message);
			} else {
				$('body').addClass('loading');
				window.location.href = 'admin/acl:view';
			}
		},
			
		errorHandler: function(event, jqXHR, message, exception) {
			errorHandler(i18n.requestError);
		}

	};
}();

$(document).ready(function() {
	App.Admin.Acl.Page.init();
});