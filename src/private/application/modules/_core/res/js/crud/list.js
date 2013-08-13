;
if (typeof AC ==="undefined") {AC = function() {}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function() {}};

AC.Crud.List = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;

	var searchTimer;
	var afterUpdateViewCallback;
	
	var viewTypeClasses = 'list grid';
	var disableMultiple = false;
	
	var isIframe = false;

	return {

		init: function() {
			$('form.ACCrudList tbody tr').live('click', AC.Crud.List.trHandler);
			$('form.ACCrudList tbody tr').live('dblclick', AC.Crud.List.editHandler);
            
            $('form.ACCrudList thead a.listView').live('click', AC.Crud.List.activateListView);
            $('form.ACCrudList thead a.gridView').live('click', AC.Crud.List.activateGridView);
            $('form.ACCrudList thead a.filterToggle').live('click', AC.Crud.List.toggleFilters);

			$('form.ACCrudList input.id').live('click', AC.Crud.List.checkboxHandler);
			$('form.ACCrudList input.toggleSelect').live('click', AC.Crud.List.toggleSelectHandler);
			$('form.ACCrudList td.toolbar a.new').live('click', AC.Crud.List.newHandler);
			$('form.ACCrudList td.toolbar a.delete').live('click', AC.Crud.List.multipleDeleteHandler);
			$('form.ACCrudList td.buttons a.edit').live('click', AC.Crud.List.editHandler);
			$('form.ACCrudList td.buttons a.delete').live('click', AC.Crud.List.deleteHandler);

			$('form.ACCrudList td.buttons a.prev').live('click', AC.Crud.List.prevHandler);
			$('form.ACCrudList td.buttons a.next').live('click', AC.Crud.List.nextHandler);
            $('form.ACCrudList td.buttons a.page').live('click', AC.Crud.List.pageHandler);
			$('form.ACCrudList td.buttons select.pageSize').live('change', AC.Crud.List.pageSizeHandler);
			$('form.ACCrudList th a.order').live('click', AC.Crud.List.orderHandler);
			$('form.ACCrudList th select.filter').live('change', AC.Crud.List.filterSelectHandler);
			$('form.ACCrudList th input[name=\'view[search]\']').live('keypress', AC.Crud.List.searchBoxHandler);
			$('form.ACCrudList th input[name=\'view[search]\']').live('search', AC.Crud.List.searchBoxHandler);
			$('form.ACCrudList th a.search').live('click', AC.Crud.List.searchButtonHandler);

			$('form.ACCrudList').bind('result', function(events, data) {
				//console.log(data);
			});
		
			// Popup functions
			isIframe = (window.location != window.parent.location) || window.opener;
			disableMultiple = ( $('form.ACCrudList table').data('disable-multiple') == '1' );
			
			// Sub init
			AC.Crud.List.initPicker();
			AC.Crud.List.initMove();
		},

		initMove: function() {
			if ($('form.ACCrudList tbody td.sort').length) {
				$('form.ACCrudList table tbody').tableDnD({
					onDrop: AC.Crud.List.onSortHandler,
					serializeParamName: 'id',
					serializeRegexp: /[^\-]*$/,
					dragHandle: '.sort'
				});
			}
		},
			
		initPicker: function() {
			if (isIframe) {
				$('form.ACCrudList tbody tr').off('dblclick');
				$('form.ACCrudList tbody tr').live('dblclick', function(e) {
					var row = $(this);
					var checkbox = row.find('input[type=checkbox]');
					checkbox.attr('checked', true);
					AC.Crud.List.chooseHandler.call(this);
				});
				if (disableMultiple) {
					$('form.ACCrudList input.toggleSelect').css('visibility', 'hidden');
				}
				$('form.ACCrudList div.form-actions a.choose').click(AC.Crud.List.chooseHandler);
				$('form.ACCrudList div.form-actions a.cancel').click(AC.Crud.List.cancelHandler);
			}
		},
			
		cancelHandler: function() {
			if (isIframe) {
				parent.$.fancybox.close();
			}
		},
			
		chooseHandler: function(e) {
			var form = $(this).parents('form.ACCrudList');
			var data = form.serializeArray();
			var rows = [], id;
			
			for (elm in data) {
				if (data[elm].name == 'id[]') {
					rows.push(data[elm].value);					
				}
			}
			
			if (window.opener) {
				// assume CKEditor for now
				
				// look for CKEditorFuncNum
				var vars = [], hash;
			    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
			    for(var i = 0; i < hashes.length; i++)
			    {
			        hash = hashes[i].split('=');
			        vars.push(hash[0]);
			        vars[hash[0]] = hash[1];
			    }
			    
			    var firstId = rows[0];
			    var url = $("#row-" + firstId).data('path');
			    
			    if (vars['link'] == 1) {
			    	var dialog = window.opener.CKEDITOR.dialog.getCurrent();
			    	dialog.setValueOf('info', 'url', url);
			        dialog.setValueOf('info', 'protocol', '');
			    } else if (vars['media'] == 1) {
			    	var CKEditorFuncNum = vars['CKEditorFuncNum'];
			    	window.opener.CKEDITOR.tools.callFunction(CKEditorFuncNum, url);
			    }
				window.close();
				
			} else if (isIframe) {
				parent.AC.Crud.Edit.Picker.chosen(rows);
			}
			
			return false;
		},

		trHandler: function(e) {
			e.stopPropagation();
			//e.preventDefault();
			var row = $(this);
			if (row.parents('table').data('singleclick') == 1) {
				AC.Crud.List.editHandler.call(this, e);
			} else {
				var checkbox = row.find('input[type=checkbox].id');
				checkbox.attr('checked', !checkbox.attr('checked'));
				AC.Crud.List.checkboxHandler.call(checkbox, e);
			}
			//return false;
		},

		checkboxHandler: function(e) {
			e.stopPropagation();
			var form = $(this).parents('form');
			var data = form.serializeArray();
			form.find('td.toolbar .delete').removeClass('show');
			form.find('input.toggleSelect').attr('checked', false);
			var count = 0;
			for (elm in data) {
				if (data[elm].name == 'id[]') {
					form.find('input.toggleSelect').css('opacity', 0.45);
					form.find('input.toggleSelect').attr('checked', true);
					form.find('td.toolbar .delete').addClass('show');
					count++;
				}
			}
			if (count == $('input.id').length || count == 0) {
				form.find('input.toggleSelect').css('opacity', 1);
			}
			if (disableMultiple) {
				form.find('input.id').attr('checked', false);
				$(this).attr('checked', true);
			}
			AC.Crud.List.updateCheckRows(this);
		},

		toggleSelectHandler: function() {
			var form = $(this).parents('form');
			var data = form.serializeArray();

			var count = 0;
			for (elm in data) {
				if (data[elm].name == 'id[]') {
					count++;
				}
			}

			form.find('input.toggleSelect').css('opacity', 1);
			if (count > 0) {
				form.find('input.id').attr('checked', false);
				form.find('td.toolbar .delete').removeClass('show');
			} else {
				form.find('input.id').attr('checked', true);
				form.find('td.toolbar .delete').addClass('show');
			}
			AC.Crud.List.updateCheckRows(this);
		},
			
		updateCheckRows: function(node) {
			var form = $(node).parents('form');
			form.find('input.id').each(function() {
				if ($(this).is(':checked')) {
					$(this).parents('tr').addClass('checked');
				} else {
					$(this).parents('tr').removeClass('checked');
				}
			});
		},

		newHandler: function() {
			if ($(this).parents('table').data('newaction')) {
				var newaction = $(this).parents('table').data('newaction');
				window.location.href = newaction + (newaction.indexOf('?') > -1 ? '&' : '?') + 'new';
			} else {
				window.location.href = window.location.pathname + '?new';
			}
		},

		editHandler: function(e) {
			e.stopPropagation();
			if ($(this)[0].nodeName == 'TR' || $(this)[0].nodeName == 'tr') {
				var row = $(this);
			} else {
				var row = $(this).parents('tr');
			}
			var id = row.find('input[type=checkbox]').attr('value');
			var form = $(this).parents('form');
			
			if (row.parents('table').data('editaction')) {
				window.location.href = row.parents('table').data('editaction') + '?edit=' + id;
			} else {
				window.location.href = window.location.pathname + '?edit=' + id;
			}
		},

		multipleDeleteHandler: function(e, id) {
			id = id || false;
			var form = $(this).parents('form');
			var self = this;

			form.find('input.operation').val('delete');

			var options = {
				operation	: 'delete',
				crudId		: form.attr('id')
			};
			var url = form.attr('action') + "?" + $.param(options);
			var data = form.serializeArray();

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

			if (count > 0 && confirm(i18n.confirmDelete + ' (' + count + ' item/items)')) {
				$('body').addClass('loading');
				$.post(url, data, function(response) {
					$('body').removeClass('loading');
					if (response.operation === 'delete' && response.success === true) {
						for (elm in data) {
							if (data[elm].name == 'id[]') {
								form.find('input.id[value=' + data[elm].value + ']').parents('tr').fadeOut(function() {
									$(this).remove();
								});
							}
						}
						AC.Core.Alert.flash(response.message);
						form.find('td.toolbar .button.multiple').removeClass('show');
					} else {
						errorHandler('Not all records could be deleted');
						AC.Crud.List.updateView(self);
					}
				}, 'json').error(function(jqXHR, message, exception) {
					$('body').removeClass('loading');
					if (exception == 'Unauthorized' || exception == 'Forbidden') {
						warningHandler(i18n.forbiddenWarning);
					} else {
						errorHandler(i18n.requestError + ' (' + exception + ')');
					}
				});
			}
		},

		onSortHandler: function(table, row) {
			var form = $(table).parents('form');

			form.find('input.operation').val('sort');

			var options = {
				operation	: 'sort',
				crudId		: form.attr('id')
			};
			var url = form.attr('action') + "?" + $.param(options);
			var data = $(table).tableDnDSerialize();

			// Add CSRF token
			data = data + '&_token=' + form.find('input[name=\'_token\']').val();

			// Add sort fieldname
			data = data + '&field=' + form.find('td.sort:eq(0)').attr('data-field');

			$.post(url, data, function(response) {
				if (response.operation === 'sort' && response.success === true) {
					AC.Core.Alert.flash(response.message);
				}
			}, 'json').error(function(jqXHR, message, exception) {
				$('body').removeClass('loading');
				if (exception == 'Unauthorized' || exception == 'Forbidden') {
					warningHandler(i18n.forbiddenWarning);
				} else {
					errorHandler(i18n.requestError + ' (' + exception + ')');
				}
			});
		},

		deleteHandler: function(e) {
			e.stopPropagation();
			var self = this;
			var row = $(this).parents('tr');
			var id = row.find('input[type=checkbox]').attr('value');
			AC.Crud.List.multipleDeleteHandler.call(self, e, id);
		},

		prevHandler: function(e) {
			if ($(this).hasClass('disabled')) {
				return;
			}
			var form = $(this).parents('form');
			var $page = form.find('input[name=\'view[page]\']');
			$page.val(parseInt($page.val()) - 1);
			AC.Crud.List.updateView(this);
		},

		nextHandler: function(e) {
			if ($(this).hasClass('disabled')) {
				return;
			}
			var form = $(this).parents('form');
			var $page = form.find('input[name=\'view[page]\']');
			$page.val(parseInt($page.val()) + 1);
			AC.Crud.List.updateView(this);
		},
                
        pageHandler: function(e) {
            if ($(this).hasClass('active')) {
				return;
			}
			var form = $(this).parents('form');
			var $page = form.find('input[name=\'view[page]\']');
			$page.val(parseInt($(this).text()));
			AC.Crud.List.updateView(this);
        },
		
		pageSizeHandler: function(e) {
			AC.Crud.List.resetPage(this);
			AC.Crud.List.updateView(this);
		},

		orderHandler: function(e) {
			var form = $(this).parents('form');
			var $orderBy = form.find('input[name=\'view[orderBy]\']');
			var $orderDir = form.find('input[name=\'view[orderDir]\']');
			$orderBy.val($(this).attr('data-orderBy'));
			$orderDir.val($(this).attr('data-orderDir'));
			AC.Crud.List.resetPage(this);
			AC.Crud.List.updateView(this);
		},

		filterSelectHandler: function(e) {
			AC.Crud.List.resetPage(this);
			AC.Crud.List.updateView(this);
		},

		searchBoxHandler: function(e) {
			var self = this;
			if (e.type == 'search' && !$(this).val()) {
				AC.Crud.List.searchButtonHandler.call(this, e, focus);
			} else if (e.which && e.which == 13) {
				e.preventDefault();
				AC.Crud.List.searchButtonHandler.call(this, e, focus);
			} else {
				clearTimeout(searchTimer);
				searchTimer = setTimeout(function() {
					AC.Crud.List.searchButtonHandler.call(self, e, focus);
				}, 1000);
			}
		},

		searchButtonHandler: function(e, c) {
			clearTimeout(searchTimer);
			AC.Crud.List.resetPage(this);
			AC.Crud.List.updateView(this, c);
		},
			
		resetPage: function(node) {
			var form = $(node).parents('form');
			var $page = form.find('input[name=\'view[page]\']');
			$page.val(1);
		},

		updateView: function(node, c) {
			var form = $(node).parents('form');
			var data = form.serializeArray();

			var count = 0;
			for (var elm in data) {
				if (data[elm].name.substr(0, 5) !== 'view[') {
					delete data[elm];
				}
			}

			form.find('tbody').animate({
				opacity: 0
			}, 'fast');

			data.push({
				name: 'output',
				value: 'table'
			});
			var url = document.location.href;

			$.get(url, data, function(response) {
				form.html($(response).find('form.ACCrudList').html());
				form.find('tbody').css({opacity: 1});
				AC.Crud.List.initMove();
				AC.Crud.List.initPicker();
				if (typeof c == 'function') {
					c();
				}
				if (typeof afterUpdateViewCallback == 'function') {
					afterUpdateViewCallback();
				}
				$.scrollTo(form, 800, { axis: 'y', offset: -70 });
			}, 'html').error(function(jqXHR, message, exception) {
				$('body').removeClass('loading');
				if (exception == 'Unauthorized' || exception == 'Forbidden') {
					warningHandler(i18n.forbiddenWarning);
				} else {
					errorHandler(i18n.requestError + ' (' + exception + ')');
				}
			});
		},

		afterUpdateView: function(callback) {
			afterUpdateViewCallback = callback;
		},
                
        activateListView: function(e) {
			var form = $(this).parents('form');
			AC.Crud.List.activateView(this, 'list', form);
        },
                
        activateGridView: function(e) {
			var form = $(this).parents('form');
			AC.Crud.List.activateView(this, 'grid', form);
        },
				
		activateView: function(node, view, form) {
//			form.find('table').removeClass(viewTypeClasses).addClass(view);
			form.find('input[name=\'view[viewType]\']').val( view );
			AC.Crud.List.updateView(node);
		},
                
        toggleFilters: function(e) {
			var form = $(this).parents('form');
			
			form.find('tr.filters').toggleClass('visible');
			form.find('input[name=\'view[filterVisible]\']').val( $('tr.filters').hasClass('visible') ? '1' : '0' );
        }

	};
}();

$(document).ready(function() {
	AC.Crud.List.init();
});