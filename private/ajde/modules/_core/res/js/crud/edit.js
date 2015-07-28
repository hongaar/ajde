;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};

AC.Crud.Edit = function() {
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var isIframe = false;
    var isDirty = false;

    var autosaveThrottleTimer;
    var autosaveTimer;
    var autosaveThrottleActive = false;
    var autosaveThrottleTimeout = 2000;
	
	return {
		
		init: function() {
			var self = this;
			
			isIframe = (window.location != window.parent.location) || (window.location.href.indexOf('CKEditor=content') != -1);
			
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

            // Set form input elements
            input = ':input';

            // Trigger dirty and autosave on form input elements
            if (!isIframe) {
                // Dirty handler for form input elements
//                $('form.ACCrudEdit:not(.autosave)').find(input).on('change', AC.Crud.Edit.setDirty);
                $('form.ACCrudEdit').find(input).on('change', AC.Crud.Edit.setDirty);

                // Autosave handlers
                $('form.ACCrudEdit.autosave').find(input).not('.noAutosave').on('change', function(e) {
                    var self = this;
                    setTimeout(function() {
                        AC.Crud.Edit.autoSave.call(self, e);
                    }, 100);
                });
                $('body').on('click', '.autosave-retry', AC.Crud.Edit.autoSave);
                $('body').on('click', '.autosave-force', function(e) {
                    //if (!autosaveThrottleActive) {
                        AC.Crud.Edit.autoSave.call(this, e);
                    //}
                });

                // Hide non-autosave buttons
                $('form.ACCrudEdit.autosave').each(function() {
                    $(this).find('.regular-group').hide();
                });
                $('form.ACCrudEdit:not(.autosave)').each(function() {
                    $(this).find('.autosave-group').hide();
                });
            } else {
                // Dirty handler for form input elements
                $('form.ACCrudEdit').find(input).on('change', AC.Crud.Edit.setDirty);

                // Hide autosave buttons
                $('form.ACCrudEdit.autosave').each(function() {
                    $(this).find('.autosave-group').hide();
                });
            }
			
		},
            
        setDirty: function(e) {
            $(this).parents('form.ACCrudEdit').find('.btn.cancel')
                    .text('discard changes')
                    .addClass('btn-danger');
            isDirty = true;
            $('.autosave-status').removeClass('active').html('<a class="autosave-force" href="javascript:void(null);">Save now</a>');
            $(window).on("beforeunload", function(e) {
                if (isDirty) {
                    return 'You have unsaved changes, are you sure you want to navigate away from this page?';
                }
            });            
        },

        setClean: function(e) {
            $(this).parents('form.ACCrudEdit').find('.btn.cancel')
                .text('back')
                .removeClass('btn-danger');
            isDirty = false;
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
                    // Don't execute for unchecked radio boxes
                    if (!$this.is(':checked')) return;

					if ($this.filter(':checked').length) {
						val = $(':input[name=' + name + ']:checked').val();
					}
                    //if (!val) return; // Don't execute for empty (unchecked) values
				} else if ($this[0].nodeName === 'SELECT') {
					val = $this.find('option:selected').val();
				} else {
					val = $this.val();
				}
				val = val.toLowerCase().replace(/ /g, '');

                if (val == '') val = '%EMPTY%';
				
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
            if ($(this).attr('disabled')) return;
			if (isIframe) {
				parent.$.fancybox.close();
			} else {
                $(window).off("beforeunload");
				window.location.href = window.location.pathname;
			}
		},

        autoSave: function(e) {
            var self = this;
            if (!autosaveThrottleActive) {
                autosaveThrottleActive = true;
                AC.Crud.Edit.saveHandler.call(self, e, 'autosave');
            } else {
                if (!autosaveTimer) {
                    $('.autosave-status').text('Saving due any moment now');
                    autosaveTimer = setTimeout(function() {
                        autosaveThrottleActive = true;
                        AC.Crud.Edit.saveHandler.call(self, e, 'autosave');
                        autosaveTimer = null;
                    }, autosaveThrottleTimeout);
                }
            }
            clearTimeout(autosaveThrottleTimer);
            autosaveThrottleTimer = setTimeout(function() {
                autosaveThrottleActive = false
            }, autosaveThrottleTimeout);
        },
		
		saveHandler: function(e, returnTo) {
			returnTo = typeof(returnTo) === 'undefined' ? 'list' : returnTo;
			var form = $(this).parents('form.ACCrudEdit');
            var self = this;
			var disableOnSave = 'button.save, button.apply, a.cancel';
			
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
                fromAutoSave: (returnTo == 'autosave' ? '1' : '0'),
				crudId		: form.attr('id')					
			};
			
			var url = $(form).attr('action') + "?" + $.param(options);
			var data = $(form).serializeArray();
			
			// clean up errors
			form.find(':input').parent().removeClass('validation_error');
			form.find('span.validation-message').remove();
            form.find('.control-group').removeClass('error');
			AC.Crud.Edit.equalizeForm();

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

            // ========= Set loading UI feedback

            // Disable submit button
            form.find(disableOnSave).attr('disabled', 'disabled');

            if (returnTo == 'autosave') {
                $('.autosave-status').addClass('active').text('Saving...');
            } else {
                // Set loading state
                $('body').addClass('loading');

                // Show info banner
                if (form.find(':input[name=form_submission]').val()) {
                    infoHandler(i18n.formSubmitting);
                } else {
                    infoHandler(i18n.saving);
                }
            }

			$.post(url, data, function(data) {
								
				if (data.success === false) {
					
					$('body').removeClass('loading');
                    $('.autosave-status').removeClass('active').html('Changes not saved, <a class="autosave-retry" href="javascript:void(null);">try again</a>');
                    AC.Crud.Edit.setDirty.call(self, e);
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
                        if (returnTo != 'autosave') {
						    $.scrollTo($('.control-group.error:first'), 800, { axis: 'y', offset: -70 });
                            if (form.find(':input[name=form_submission]').val()) {
								warningHandler(i18n.formPleaseCorrect);
                                //AC.Core.Alert.hide();
                            } else {
                                warningHandler(i18n.formPleaseCorrect);
                            }
                        }
					} else {
						errorHandler(i18n.applicationError);
					}
				} else {
                    isDirty = false;
					if (typeof $(form[0]).data('onSave') === 'function') {
						var fn = $(form[0]).data('onSave');
						if (fn(data) === false) {
							
							$('body').removeClass('loading');
                            setTimeout(function() {
                                $('.autosave-status').removeClass('active').text("All changes saved").addClass('flash');
                                setTimeout(function() {
                                    $('.autosave-status').removeClass('flash');
                                }, 50);
                            }, 100);
                            AC.Crud.Edit.setClean.call(self, e);
							form.find(disableOnSave).attr('disabled', null);
							
							return;
						}
					}					
					if (isIframe && parent.AC && parent.AC.Crud && parent.AC.Crud.Edit) {
						if (data.operation === 'save') {
							parent.AC.Crud.Edit.Multiple.editSaved(data.id, data.displayField);
						} else {
							parent.AC.Crud.Edit.Multiple.newSaved(data.id, data.displayField);
						}
					} else {
                        if (returnTo == 'autosave') {
                            setTimeout(function() {
                                $('.autosave-status').removeClass('active').text("All changes saved").addClass('flash');
                                setTimeout(function() {
                                    $('.autosave-status').removeClass('flash');
                                }, 50);
                            }, 100);
                            AC.Crud.Edit.setClean.call(self, e);
                            form.find(disableOnSave).attr('disabled', null);
                            // set id in case in insert operation
                            if (data.operation === 'insert') {
                                form.find(':input[name=id]').val(data.id);
                            }
                            // replace url
                            history.replaceState({}, false, location.href.replace('?new', '?edit=' + data.id));
                        } else if (returnTo) {
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
                $('.autosave-status').removeClass('active').html('Changes not saved, <a class="autosave-retry" href="javascript:void(null);">try again</a>');
                AC.Crud.Edit.setDirty.call(self, e);
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