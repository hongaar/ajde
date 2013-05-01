//;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};

AC.Crud.Edit = function() {
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var isIframe = false;
    var isDirty = false;
	
	return {
		
		init: function() {
			var self = this;
			
			isIframe = (window.location != window.parent.location);
			
			if (isIframe) {
				$('form.ACCrudEdit button.apply').hide();
			}
			
			$('form.ACCrudEdit a.cancel').click(AC.Crud.Edit.cancelHandler);
			$('form.ACCrudEdit button.save').click(AC.Crud.Edit.saveHandler);
			$('form.ACCrudEdit button.apply').click(function(e) {
				var self = this;
				AC.Crud.Edit.saveHandler.call(self, e, 'edit');
			});
			
			AC.Shortcut.add('Ctrl+S', AC.Crud.Edit.saveHandler);
			        
            // Dirty handler for form input elements
            $('form.ACCrudEdit :input').on('change', AC.Crud.Edit.setDirty);
		},
            
        setDirty: function(e) {
            $(this).parents('form.ACCrudEdit').find('.btn.cancel')
                    .text('discard changes')
                    .addClass('btn-danger');
            isDirty = true;
            $(window).on("beforeunload", function(e) {
                if (isDirty) {
                    return 'You have unsaved changes, are you sure you want to navigate away from this page?';
                }
            });            
        },
		
		equalizeForm: function() {
			// Deprecated
		},
		
		cancelHandler: function() {
			if (isIframe) {
				parent.$.fancybox.close();
			} else {
                $(window).off("beforeunload");
				window.location.href = window.location.pathname;
			}
		},
		
		saveHandler: function(e, returnTo) {
			returnTo = typeof(returnTo) === 'undefined' ? 'list' : returnTo;
			var form = $(this).parents('form.ACCrudEdit');
			var disableOnSave = 'button.save, button.apply, button.cancel';
			
			if (!form.length) {
				form = $('form.ACCrudEdit:eq(0)');
			}
			
			// TODO: HTML5 validation, should be deprecated?
			if (form[0].checkValidity) {
				if (form[0].checkValidity() === false) {
					errorHandler(i18n.formError);
					return false;
				};
			}
			
			var options = {
				operation	: 'save',
				fromIframe	: (isIframe ? '1' : '0'),
				crudId		: form.attr('id')					
			};
			
			var url = $(form).attr('action') + "?" + $.param(options);
			var data = $(form).serialize();
			
			// clean up errors
			form.find(':input').parent().removeClass('validation_error');
			form.find('span.validation-message').remove();
			AC.Crud.Edit.equalizeForm();
			
			// Set loading state and disable submit button
			$('body').addClass('loading');
			form.find(disableOnSave).attr('disabled', 'disabled');
			
			if (typeof $(form[0]).data('onBeforeSubmit') === 'function') {
				var fn = $(form[0]).data('onBeforeSubmit');
				if (fn() === false) {
					form.find(disableOnSave).attr('disabled', null);
					return false;
				}
			}
			$.post(url, data, function(data) {		
								
				if (data.success === false) {
					
					$('body').removeClass('loading');
					form.find(disableOnSave).attr('disabled', null);
				
					if (data.errors) {
						if (typeof $(form[0]).data('onError') === 'function') {
							var fn = $(form[0]).data('onError');
							fn();
						}
						for(var i in data.errors) {
                            $input = $(':input[name=' + i + ']');
							$parent = $input.parents('.control-group');
							if (!$parent.length) {
								errorHandler('Field \'' + i + '\' has errors but is hidden');
							}
							$parent.addClass('error');
							firstError = data.errors[i][0];
							$parent.data('message', firstError);
							$message = $('<span class="help-block badge badge-important validation-message"></span>').html(firstError).hide();
							$input.after($message.fadeIn());
							AC.Crud.Edit.equalizeForm();
						}
						$.scrollTo($('.control-group.error:first'), 800, { axis: 'y', offset: -70 });
					} else {
						errorHandler(i18n.applicationError);
					}
				} else {
                    isDirty = false;
					if (typeof $(form[0]).data('onSave') === 'function') {
						var fn = $(form[0]).data('onSave');
						if (fn(data) === false) {
							
							$('body').removeClass('loading');
							form.find(disableOnSave).attr('disabled', null);
							
							return;
						}
					}					
					if (isIframe) {
						if (data.operation === 'save') {
							parent.AC.Crud.Edit.Multiple.editSaved(data.id, data.displayField);
						} else {
							parent.AC.Crud.Edit.Multiple.newSaved(data.id, data.displayField);
						}
					} else {
                        if (returnTo) {
                            window.location.href = window.location.pathname + '?' + returnTo + '=' + data.id;
                        } else {
                            $('body').removeClass('loading');
                            form.find(disableOnSave).attr('disabled', null);
                            AC.Core.Alert.flash('Record saved');
                        }
					}
				}
			}, 'json').error(function(jqXHR, message, exception) {
				
				$('body').removeClass('loading');
				form.find(disableOnSave).attr('disabled', null);
				
				if (typeof $(form[0]).data('onError') === 'function') {
					var fn = $(form[0]).data('onError');
					fn();
				}
				if (exception == 'Unauthorized' || exception == 'Forbidden') {
					warningHandler(i18n.forbiddenWarning);
				} else {
					errorHandler(i18n.requestError + ' (' + exception + ')');
				}
			});
			
			return false;
		}
	};
}();

$(document).ready(function() {
	AC.Crud.Edit.init();
});