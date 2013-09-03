;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Admin === "undefined") {App.Admin = function(){}};
if (typeof App.Admin.System === "undefined") {App.Admin.System = function(){}};

App.Admin.System.Update = function() {
	
	var timer;
	var step = 'download';
	var xhr;
	
	var form;
	var button;
	
	var update = function(curstep)
	{
		var statusText;
		
		switch (curstep) {
			case 'download':
				statusText = 'Downloading...';
				nextStep = 'extract';
				break;
			case 'extract':
				statusText = 'Extracting files...';
				nextStep = 'clear';
				break;
			case 'clear':
				statusText = 'Clean current installation...';
				nextStep = 'copy';
				break;
			case 'copy':
				statusText = 'Copying files...';
				nextStep = 'post';
				break;
			case 'post':
				statusText = 'Running post hook...';
				nextStep = 'done';
				break;
		}
		
		$('body').removeClass('loading');
		button.prop('disabled', 'disabled');
		button.text(statusText);
		
		step = nextStep;
				
	};
	
	var onUpdateStepComplete = function(e) {
		if (step == 'done') {
			onUpdateComplete(e);
		} else {
			$('input[name=step]').val(step);
			form.trigger('submit');
			update(step);		
		}
	};
	
	var onUpdateComplete = function(e) {
		button
			.text('Done, refreshing... 	')
			.removeClass('btn-primary')
			.addClass('btn-success');
//		clearInterval(timer);
		setTimeout(function() {
			window.location.reload(true);
		}, 2000);
	};
	
	var onUpdateStatus = function(e) {
        
	};

	return {

		init: function() {
			button = $('#systemUpdateForm button');
			form = $('#systemUpdateForm');
			
			form.on('result', onUpdateStepComplete);
		}

	};
}();

$(document).ready(function() {
	App.Admin.System.Update.init();
});