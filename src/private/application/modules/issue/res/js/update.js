;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Issue === "undefined") {App.Issue = function(){}};

App.Issue.Update = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	return {

		init: function() {			
			// ...
		},
		
		update: function(id, meta, key, value) {
			
			var url = 'issue/update.json?id=' + id;
			var data = {
				"meta": meta,
				"key": key,
				"value": value,				
				"_token": $('input[name=_token]').val()
			};
			$('body').addClass('loading');
			$.post(url, data, function(data) {
				$('body').removeClass('loading');
				if (data.success === true) {
					AC.Core.Alert.flash(data.message);
				} else {
					warningHandler(data.message);
				}
			}, 'json').error(function(jqXHR, message, exception) {
				$('body').removeClass('loading');
				if (exception == 'Unauthorized' || exception == 'Forbidden') {
					warningHandler(i18n.forbiddenWarning);
				} else {
					errorHandler(i18n.requestError + ' (' + exception + ')');
				}
			});
		}

	};
}();

$(document).ready(function() {
	App.Issue.Update.init();
});