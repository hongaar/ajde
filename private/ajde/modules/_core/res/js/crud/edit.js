;
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
			AC.Shortcut.add('Ctrl+Alt+S', function(e) {
				var self = this;
				AC.Crud.Edit.saveHandler.call(self, e, 'edit');
			});

			// Show/hide elements with data-show-[fieldname] set, and run now
			$('form.ACCrudEdit :input').on('change', AC.Crud.Edit.dynamicFields).each(function() {
				AC.Crud.Edit.dynamicFields.call(this);
			});
			
			// Hide fieldsets with no control-groups
			$('form.ACCrudEdit fieldset.crud').each(function() {
				if (!$(this).find('.control-group:visible').length) {
					$(this).stop(true, true).hide('fast');
				}
			});
			
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
			
		dynamicFields: function(e) {
			var $this = $(this);
			var val = "";
			var name = $this.attr('name');
			
			if (name && name.indexOf('[]') === -1 && $('.control-group[data-show-' + name + ']').length) {
				
				// control group
				var ctlgroup = $('.control-group[data-show-' + name + ']');
				
				// get value
				if ($this.attr('type') === 'radio') {
					if ($this.filter(':checked').length) {
						val = $(':input[name=' + name + ']:checked').val();
					}
				} else if ($this[0].nodeName === 'SELECT') {
					val = $this.find('option:selected').val();
				} else {
					val = $this.val();
				}
				val = val.toLowerCase().replace(/ /g, '');
				
				if (val) {
					
					var $hidden = ctlgroup.filter(':not([data-show-' + name + '*="|' + val + '|"])');
					var $shown = ctlgroup.filter('.control-group[data-show-' + name + '*="|' + val + '|"]');
					
					// dynamic sort the shown fields
					AC.Crud.Edit.dynamicSort($shown, name, val);
					
					$hidden.stop(true, true).hide();
					$hidden.each(function() {
						var $self = $(this);
						setTimeout(function() {
							if (!$self.parents('fieldset').find('.control-group:visible').length) {
								$self.parents('fieldset').stop(true, true).hide('fast');
							}
						}, 100);
					});
					$shown.parents('fieldset').show();
					$shown.removeClass('dynamic').stop(true, true).fadeIn();
				}				
			}		
		},
		
		dynamicSort: function($ctlgroups, field, search) {
			var groups = $ctlgroups.filter('[data-sort-' + field + ']');
			var container = groups.parent();
			groups.each(function() {
				var dataShow = $(this).data('show-' + field);
				dataShow = dataShow.substring(1, dataShow.length - 1).split('|');
				var dataSort = $(this).data('sort-' + field);
				dataSort = dataSort.substring(1, dataSort.length - 1).split('|');
				var index = dataShow.indexOf(search);
				var sort = dataSort[index];
				$(this).data('sort', sort);
			});
			groups.detach().sort(function(a, b) {
				return parseInt($(a).data('sort')) > parseInt($(b).data('sort')) ? 1 : -1;  
			});
			container.append(groups);			
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
			var data = $(form).serializeArray();
			
			// clean up errors
			form.find(':input').parent().removeClass('validation_error');
			form.find('span.validation-message').remove();
			AC.Crud.Edit.equalizeForm();
			
			// Set loading state and disable submit button
			$('body').addClass('loading');
			form.find(disableOnSave).attr('disabled', 'disabled');
			
			// remove hidden meta fields
			for (var elm in data) {
				if (data[elm].name.substring(0, 5) === 'meta_') {
					if (!$(':input[name="' + data[elm].name + '"]').closest('.control-group').is(':visible')) {
						delete data[elm];
					}
				}
			}
			
			if (typeof $(form[0]).data('onBeforeSubmit') === 'function') {
				var fn = $(form[0]).data('onBeforeSubmit');
				if (fn() === false) {
					$('body').removeClass('loading');
					form.find(disableOnSave).attr('disabled', null);
					e.preventDefault();
					return false;
				}
			}
		
			infoHandler('Saving...');
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
                            $input = $(':input[name=' + i + '], :input[name="' + i + '[]"]');
							$parent = $input.parents('.control-group');
							if (!$parent.length || $parent.is(':not(:visible)')) {
								errorHandler('Field \'' + i + '\' has errors but is hidden');
							}
							$parent.addClass('error');
							firstError = data.errors[i][0];
							$parent.data('message', firstError);
							$message = $('<span class="help-block badge badge-important validation-message"></span>').html(firstError).hide();
							$input.parents('.controls').append($message.fadeIn());
							AC.Crud.Edit.equalizeForm();
						}
						$.scrollTo($('.control-group.error:first'), 800, { axis: 'y', offset: -70 });
						warningHandler('Please correct the errors in this form');
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