;
if (typeof AC === "undefined") {AC = function(){}};
if (typeof AC.Admin === "undefined") {AC.Admin = function(){}};
if (typeof AC.Admin.Media === "undefined") {AC.Admin.Media = function(){}};

AC.Admin.Media.Upload = function() {
		
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var onComplete = function(e, id, filename, response) {
		filename = response.filename;
		$('input[name=filename]').val(filename);
		$('#mediaUpload').submit();
	};

	var onCallback = function(e, data) {
		if (!data.success) {
			errorHandler('Something went wrong when saving the media file');
		}
	};

	return {
		
		init: function() {

			$('div.ACAjaxUpload').bind('completeUpload', onComplete);
			$('#mediaUpload').bind('result', onCallback);
			
		}
		
	};
}();

$(document).ready(function() {
	AC.Admin.Media.Upload.init();
});