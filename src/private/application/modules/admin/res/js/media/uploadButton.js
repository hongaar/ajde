;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Admin === "undefined") {App.Admin = function(){}};
if (typeof App.Admin.Media === "undefined") {App.Admin.Media = function(){}};

App.Admin.Media.UploadButton = function() {
		
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var node;
	var uploading = 0;
	
	var onStart = function(e, id, filename) {
		node.addClass('loading');
		uploading++;
	};

	var onError = function(id, fileName, errorReason) {
		uploading--;
		onCallback({success:true});
	};

	var onComplete = function(e, id, filename, response) {
		filename = response.filename;
		nodetype = $('form.ACCrudList').find('select[name="view[filter][nodetype]"]').val();
		uploading--;
		
		var form = node.parents('form');
		var url = 'admin/media:upload.json';
		var data = {
			_token: form.find('input[name=\'_token\']').val(),
			filename: filename,
			nodetype: nodetype
		};

		$.post(url, data, onCallback, 'json').error(function(jqXHR, message, exception) {
			$('body').removeClass('loading');
			node.removeClass('loading');
			errorHandler(i18n.requestError + ' (' + exception + ')');
		});
	};

	var onCallback = function(data) {
		if (!data.success) {
			errorHandler('Something went wrong when saving the media file');
		} else {
			if (uploading === 0) {
				AC.Crud.List.updateView(node);
			}
		}
	};

	return {
		
		init: function() {
			
			AC.Crud.List.afterUpdateView(function() {
				AC.Form.Upload.init();
				App.Admin.Media.UploadButton.bind();
			});
			this.bind();
			
		},
			
		bind: function() {
			node = $('div.ACAjaxUpload');
			node.bind('startUpload', onStart);
			node.bind('errorUpload', onError);
			node.bind('completeUpload', onComplete);
		}
		
	};
}();

$(document).ready(function() {
	App.Admin.Media.UploadButton.init();
});