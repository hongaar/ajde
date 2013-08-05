;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Issue === "undefined") {App.Issue = function(){}};

App.Issue.StatusBtn = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var btnStatusHandler = function(e) {
		
		id = false;
		var form = $(this).parents('form');
		var self = this;
		var data = form.serializeArray();
		var value = $(this).text();
		
		var count = 0;
		for (elm in data) {
			if (data[elm].name == 'id[]') {
				if (id !== false && data[elm].value != id) {
					delete data[elm];
				} else {
					count++;
				}
			}
		}

		if (id !== false && count == 0) {
			data.push({
				name: 'id[]',
				value: id
			});
			count = 1;
		}
	
		data.push({
			name: 'status',
			value: value
		});
	
		if (count > 0) {
			var url = 'issue/statusBtn.json';
			$.post(url, data, function(data) {										
				if (data.success === true) {
					infoHandler(data.message);
					if (AC && AC.Crud && AC.Crud.List) {
						AC.Crud.List.updateView($('form.ACCrudList').children(':eq(0)'));
					}
				} else {
					warningHandler(data.message);
				}
			}, 'json').error(function(jqXHR, message, exception) {
				if (exception == 'Unauthorized' || exception == 'Forbidden') {
					warningHandler(i18n.forbiddenWarning);
				} else {
					errorHandler(i18n.requestError + ' (' + exception + ')');
				}
			});
		} else {
			infoHandler('Nothing selected');
		}
	};

	return {

		init: function() {			
			$('form.ACCrudList thead ul.dropdown-menu.status a').live('click', btnStatusHandler);
		}

	};
}();

$(document).ready(function() {
	App.Issue.StatusBtn.init();
});