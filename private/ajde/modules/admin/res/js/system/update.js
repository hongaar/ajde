;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Admin === "undefined") {App.Admin = function(){}};
if (typeof App.Admin.System === "undefined") {App.Admin.System = function(){}};

App.Admin.System.Update = function() {
	
	var step = 'download';
	var progress = 0;
	
	var form;
	var button;
	var statusLabel;
	var progressBar;
	
	var update = function(curstep)
	{
		var statusText;
		
		switch (curstep) {
			case 'download':
				statusText = '1/5 Downloading...';
				progress = 15;
				nextStep = 'extract';
				break;
			case 'extract':
				statusText = '2/5 Extracting files...';
				progress = 30;
				nextStep = 'clear';
				break;
			case 'clear':
				statusText = '3/5 Clean current installation...';
				progress = 40;
				nextStep = 'copy';
				break;
			case 'copy':
				statusText = '4/5 Copying files...';
				progress = 60;
				nextStep = 'post';
				break;
			case 'post':
				statusText = '5/5 Post-processing...';
				progress = 80;
				nextStep = 'done';
				break;
		}
		
		$('body').removeClass('loading');
		button.hide();
		
		$('#status').show();
		statusLabel.text(statusText);
		progressBar.css({width: progress + '%'});
		
		step = nextStep;				
	};
	
	var onUpdateStepComplete = function(e, data) {
		
		if (data.status !== true) {
			statusLabel.text(data.status === false ? 'Unknown error' : data.status);
			$('.progress').addClass('progress-danger');
			progressBar.css({width: '100%'});
		} else {		
			if (step == 'done') {
				onUpdateComplete(e);
			} else {
				$('input[name=step]').val(step);
				form.trigger('submit');
				update(step);		
			}			
		}
		
	};
	
	var onUpdateComplete = function(e) {

		statusLabel.text('Done, refreshing...');
		progressBar.css({width: '100%'});
		
		setTimeout(function() {
			window.location.reload(true);
		}, 2000);
	};
	
	return {

		init: function() {
			button = $('#systemUpdateForm button');
			form = $('#systemUpdateForm');
			statusLabel = $('#status p');
			progressBar = $('#status .bar');
			
			form.on('result', onUpdateStepComplete);
		}

	};
}();

$(document).ready(function() {
	App.Admin.System.Update.init();
});