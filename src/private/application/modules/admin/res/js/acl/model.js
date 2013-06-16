;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Admin === "undefined") {App.Admin = function(){}};
if (typeof App.Admin.Acl === "undefined") {App.Admin.Acl = function(){}};

App.Admin.Acl.Model = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	return {

		init: function() {
			$('#AclModel')
				.bind('result', this.resultHandler)
				.bind('error', this.errorHandler);
		
			$('button.toggle').on('click', this.toggleHandler);
			$('input.toggle').each(this.setButtonState);
		},
			
		toggleHandler: function(e) {
			var val = $(this).data('value');
			var container = $(this).parents('.controls:first');
			var input = container.find('input');
			if (val === 'deny' || val === 'all') {
				container.find('[data-toggle="buttons-checkbox"]').find('button').removeClass('active');
				input.val(val === 'deny' ? '' : '*');
			} else {
				container.find('[data-toggle="buttons-radio"]').find('button').removeClass('active');
				setTimeout(function() {
					if (container.find('[data-toggle="buttons-checkbox"]').find('button.active').length === 4) {
						 container.find('[data-value=all]').addClass('active');
						 container.find('[data-toggle="buttons-checkbox"]').find('button').removeClass('active');
						 input.val('*');
					} else {
						var values = container.find('[data-toggle="buttons-checkbox"]').find('button.active').map(function() {
							return $(this).data('value');
						}).get().join('|');
						if (val === 'read' && !container.find('[data-value=read]').hasClass('active')) {
							container.find('[data-toggle="buttons-checkbox"]').find('button').removeClass('active');
							values = '';
						} else if (values.length > 0 && values.indexOf('read') === -1) {
							// all operations require read access
							container.find('[data-value=read]').addClass('active');
							values = 'read|' + values; 
						}
						// require update access on insert access
						if (values.indexOf('insert') > -1 && values.indexOf('update') === -1) {
							container.find('[data-value=update]').addClass('active');
							values = 'update|' + values; 
						} 
						// require update access on delete access
						if (values.indexOf('delete') > -1 && values.indexOf('update') === -1) {
							container.find('[data-value=update]').addClass('active');
							values = 'update|' + values; 
						} 
						if (values === '') {
							container.find('[data-value=deny]').addClass('active');
						}
						input.val(values);
					}
				}, 0);
			}			
		},
			
		setButtonState: function() {
			var val = $(this).val().split('|');
			var container = $(this).parent();
			for(var i in val) {
				if (val[i] == '') {
					container.find('button[data-value=deny]').addClass('active');
				} else if (val[i] == '*') {
					container.find('button[data-value=all]').addClass('active');
				} else {
					container.find('button[data-value=' + val[i] + ']').addClass('active');
				}
			}
			
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
	App.Admin.Acl.Model.init();
});