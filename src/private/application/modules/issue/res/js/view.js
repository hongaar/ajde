;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Issue === "undefined") {App.Issue = function(){}};

App.Issue.View = function() {
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;

	var btnWorkHandler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		var row = $(this).parents('tr');
		var id = row.find('input[type=checkbox]').attr('value');
		App.Timer.Widget.work(id);
		return false;
	};
	
	var btnQuickIssueHandler = function(e) {
		e.stopPropagation();
		e.preventDefault();
		var row = $(this).parents('tr');
		var id = row.find('input[type=checkbox]').attr('value');
		
		$('#quickissue :input[name=parent]').val(id);
		$('#quickissue :input[name=title]').val('');
		$('#quickissue :input[name=due]').val('');
		$('#quickissue :input[name=allocated], #quickissue_span').val('');
		
		$('#quickissue').modal('show');
		
		return false;
	};
	
	return {

		init: function() {			
			$('form.ACCrudList tbody td.buttons a.work').live('click', btnWorkHandler);
			$('form.ACCrudList tbody td.buttons a.new-issue').live('click', btnQuickIssueHandler);
			$('#quickissue form')
				.bind('result', this.resultHandler)
				.bind('error', this.errorHandler);
		},
		
		resultHandler: function(event, data) {
			$('#quickissue').modal('show');
			if (data.success === false) {
				errorHandler(data.message);
			} else {
				$('#quickissue').modal('hide');
				$('body').addClass('loading');
				AC.Crud.List.afterUpdateView(function() {
					AC.Core.Alert.flash(data.message);
					$('body').removeClass('loading');
				});
				AC.Crud.List.updateView($('form.ACCrudList').children(':eq(0)'));
			}
		},
			
		errorHandler: function(event, jqXHR, message, exception) {
			errorHandler(i18n.requestError);
		}

	};
}();

$(document).ready(function() {
	App.Issue.View.init();
});