;
if (typeof AC ==="undefined") { 		AC = function() {}; }
if (typeof AC.Form ==="undefined") { 	AC.Form = function() {}; }

AC.Form.Upload = function() {
	return {
		
		init: function() {
			$('div.ACAjaxUpload').each(function() {
				var elm = $(this);
				var uploader = new qq.FineUploader({
                    
                    /** OPTIONS **/
                    
	                element: elm[0],
	                request: {
                        endpoint: '_core/component:formUpload.json',
                        params: {
                            'optionsId' : elm.attr('data-options'),
                            '_token' : elm.parents('form').find('input[name=_token]').val()
                        }
                    },	                
	                allowedExtensions: [], 
					sizeLimit: 0,   
					minSizeLimit: 0,
                    failedUploadTextDisplay: {
                        mode: 'custom',
                        maxChars: 100
                    },
                    
                    /** STYLING **/
                    
                    text: {
                        uploadButton: '<i class="icon-upload"></i> Upload file'
                    },
                    template: '<div class="qq-uploader span12">' +
                                  '<pre class="qq-upload-drop-area span12"><span>{dragZoneText}</span></pre>' +
                                  '<div class="qq-upload-button btn" style="width: auto;">{uploadButtonText}</div>' +
                                  '<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
                                  '<ul class="qq-upload-list" style="margin-top: 10px; text-align: center;"></ul>' +
                                '</div>',
                    classes: {
                        success: 'alert alert-success',
                        fail: 'alert alert-error'
                    },                    
                    
                    /** CALLBACKS **/
                    
                    callbacks: {
                        onSubmit: function(id, fileName) {
                            // Disable save button
                            elm.parents('form').find('button.save, button.apply').attr('disabled', 'disabled');
                            // Disable upload button
                            if (elm.attr('data-multiple') == '0') {
    //							elm.find('.qq-upload-button').hide();
                            }
                        },
                        onProgress: function(id, fileName, loaded, total) {},
                        onComplete: function(id, fileName, responseJSON) {
                            if (responseJSON.error) {
                                elm.find('.qq-upload-button').show();
                                elm.parents('form').find('button.save, button.apply').attr('disabled', null);
                            } else {
								elm.trigger('completeUpload', [id, fileName, responseJSON]);
                                var filename = responseJSON.filename;
                                var $input = $('input[name=' + elm.attr('data-name') + ']');
                                elm.parents('form').find('button.save, button.apply').attr('disabled', null);
                                if (elm.attr('data-multiple') == '0') {
                                    $input.val(filename).change(); // calling .change() can trigger exception??
    //								elm.find('.qq-uploader').remove();								
                                    elm.after($('<span/>')
                                        .addClass('qq-filename')
                                        .text(filename + ' ')
                                        .append($('<a/>')
                                            .attr('href', 'javascript:void(null)')
                                            .addClass('deleteFileCrud btn btn-danger')
                                            .text('delete')
                                            .click(function() {
                                                elm.trigger('resetUpload');
                                                elm.find('.qq-upload-list').empty();
                                                elm.show();
                                                $(this).parent().remove();
                                            })
                                        )
                                    );
    //								elm.remove();
                                    elm.hide();
                                } else {
                                    $input.val($input.val() + ($input.val() ? ':' : '') + filename);
                                }
                            }
                        },
                        onCancel: function(id, fileName) {
                            elm.find('.qq-upload-button').show();
                            elm.parents('form').find('button.save, button.apply').attr('disabled', null);
                        }
                    },
	                debug: true
	            });          
			});
		},
		
		changeHandler: function() {
			//alert($(this).val());
		}
	};
}();

$(document).ready(function() {
	AC.Form.Upload.init();
});