;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function(){}};
if (typeof AC.Crud.Edit ==="undefined") {AC.Crud.Edit = function(){}};

AC.Crud.Edit.Multiple = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;

	var currentCrudAction;

	var submit = function(options, callback) {
		var form = $(options.that).parents('form');
		form.find('input.operation').val(options.operation);
		var field = $(options.that).parents('div.multiple:eq(0)').attr('data-field');
		var parentId = $(options.that).parents('div.multiple:eq(0)').attr('data-parent-id');

		if (!parentId) {
			errorHandler('Please click \'save\' before adding a ' + field);
			return;
		}

		var get = {
			operation	: options.operation,
			crudId		: form.attr('id')
		};
		var url = form.attr('action') + "?" + $.param(get);

		// Add CSRF token
		var data = '_token=' + form.find('input[name=\'_token\']').val();

		// Add field + parent_id
		data = data + '&field=' + field;
		data = data + '&parent_id=' + parentId;

		// Add params
		data = data + '&' + $.param(options.params);

		$.post(url, data, callback, 'json').error(function(jqXHR, message, exception) {
			$('body').removeClass('loading');
			errorHandler(i18n.requestError + ' (' + exception + ')');
		});
	};

	var add = function(id, display, that) {
		submit({
			that:		that,
			operation:	'addMultiple',
			params:		{
							id: id
						}
		}, function(response) {
			if (response.success === true) {
				if (response.lastId) {
					addRow(id, display, that, {}, response.lastId);
				} else {
					addRow(id, display, that);
				}
				AC.Core.Alert.flash(response.message);
			} else if (response.success === false) {
				AC.Core.Alert.warning(response.message);
			}
		});
	};

	var getRowData = function(id, that, callback) {
		var form = $(that).parents('form');
		var field = $(that).parents('div.multiple:eq(0)').attr('data-field');
		form.find('input.operation').val('getMultipleRow');

		var get = {
			operation	: 'getMultipleRow',
			crudId		: form.attr('id'),
			id			: id,
			field		: field
		};
		var url = form.attr('action') + "?" + $.param(get);

		$.get(url, {}, function(response) {
			callback(response);
		}, 'json').error(function(jqXHR, message, exception) {
			$('body').removeClass('loading');
			errorHandler(i18n.requestError + ' (' + exception + ')');
		});
	};

	var addRow = function(id, display, that, data, lastId) {
		data = data || {};
		var dynamic = (!data.length && $(that).parents('div.multiple:eq(0)').attr('data-dynamic') == 1);
		if (dynamic || display === false) {
			getRowData(id, that, function(response) {
				if (!response.data.length) {
					// prevent inf. loop
					response.data = [false];
				}
				addRow(id, response.displayField, that, response.data, lastId);
			});
		} else {
			$lastrow = $(that).parents('.multipleToolbar').next().find('tbody tr:last');
			$newrow = $lastrow.clone();
			$newrow.removeClass('template');
			$newrow.find('td:eq(0)').text(id);
			$newrow.find('td:eq(1)').text(display);
			var colCounter = 2;
			for (var i in data) {
				if (!$newrow.find('td:eq(' + colCounter + ')').hasClass('sort')) {
					$newrow.find('td:eq(' + colCounter + ')').html(data[i]);
				}
				colCounter++;
			}
			$newrow.find('td').find('a.btn').attr('data-id', id);
			if (lastId) {
				$newrow.attr('id', 'row-' + lastId);
			}
			$newrow.attr('data-id', id);
			$newrow.data('id', id);
			$newrow.hide();
			$newrow.insertAfter($lastrow);
			$newrow.css('opacity', 1).fadeIn(function() {
				$(this).css('display', 'table-row');
			});
			AC.Crud.Edit.Multiple.initMove();
			AC.Crud.Edit.Multiple.initEmptyTable();
		}
	};

	var makePrefillQS = function(elm, key, value) {
		var prefills = $(elm).parents('div.multiple:eq(0)').data('prefill')
		if (prefills) {
			var ret = '';
			prefills = prefills.split(',');
			for (var i in prefills) {
				ret += key + '[' + prefills[i] + ']=';
				if (value === 1) {
					ret += '1&';
				} else {
					ret += $(elm).parents('div.multiple:eq(0)').data('prefill-' + prefills[i]) + '&';
				}
			}
			return ret;
		}
		return '';
	};

	var getPrefillFields = function(elm) {
		return makePrefillQS(elm, 'prefill', true);
	};

	var getHiddenFields = function(elm) {
		return makePrefillQS(elm, 'hide', 1);
	};

	return {

		init: function() {
			$('form.ACCrudEdit div.multiple a.newMultiple').click(AC.Crud.Edit.Multiple.newHandler);
			$('form.ACCrudEdit div.multiple a.addMultiple').click(AC.Crud.Edit.Multiple.addHandler);
			$('form.ACCrudEdit div.multiple a.editMultiple').live('click', AC.Crud.Edit.Multiple.editHandler);
			$('form.ACCrudEdit div.multiple tbody tr').live('dblclick', AC.Crud.Edit.Multiple.editHandler);
			$('form.ACCrudEdit div.multiple a.deleteMultiple').live('click', AC.Crud.Edit.Multiple.deleteHandler);
			$('form.ACCrudEdit div.picker.multiple').bind('chosen', AC.Crud.Edit.Multiple.chosenHandler);
			
			$('form.ACCrudEdit div.multiple a.imagePreview').fancybox({closeBtn: false});
			
			AC.Crud.Edit.Multiple.initMove();
			AC.Crud.Edit.Multiple.initEmptyTable();
		},

		initMove: function() {
			if ($('form.ACCrudEdit table.multipleList tbody td.sort').length) {
				$('form.ACCrudEdit table.multipleList tbody').tableDnD({
					onDrop: AC.Crud.Edit.Multiple.onSortHandler,
					serializeParamName: 'id',
					serializeRegexp: /[^\-]*$/,
					dragHandle: '.sort'
				});
			}
		},
			
		initEmptyTable: function() {
			$('form.ACCrudEdit table.multipleList').each(function() {
				if ($(this).find('tbody tr:visible').length) {
					$(this).find('thead').show();
				} else {
					$(this).find('thead').hide();
				}
			});
		},
			
		chosenHandler: function(e, ids) {
			var that = this;
			for (elm in ids) {
				add(ids[elm], false, this);
			}
		},

		newHandler: function(e) {
			var parentId = $(this).parents('div.multiple:eq(0)').attr('data-parent-id');
			var field = $(this).parents('div.multiple:eq(0)').attr('data-field');
			if (!parentId) {
				errorHandler('Please click \'save\' before adding a ' + field);
				return;
			}

			currentCrudAction = this;

			var parent = $(this).parents('div.multiple:eq(0)').attr('data-parent');
			var editRoute = $(this).parents('div.multiple:eq(0)').attr('data-edit-route');

			$.fancybox.open({
				href: editRoute + '?new&prefill[' + parent + ']=' + parentId + '&hide[' + parent + ']=1&' + getPrefillFields(this) + getHiddenFields(this),
				type: 'iframe',
				autoSize: false,
				maxWidth: 960,
				width: '100%',
				height: '100%',
				closeBtn: false
			});
		},

		newSaved: function(id, display) {
			if ($(currentCrudAction).parent().is('.simple-selector')) { // hasSimpleSelector
				$select = $(currentCrudAction).parent().find('select.chosen');
				$select.append('<option value="' + id + '" selected="selected">' + display + '</option>');
				$select.trigger('change');
				$select.trigger("liszt:updated");
				$.fancybox.close();
			} else if ($(currentCrudAction).parent().find('.addMultiple, .picker.multiple').length) { // hasCrossReferenceTable
				$.fancybox.close();
				add(id, display, currentCrudAction);
			} else {
				$.fancybox.close();
				addRow(id, display, currentCrudAction);
				AC.Core.Alert.flash($(currentCrudAction).parents('div.multiple:eq(0)').attr('data-field') + ' added');
			}
		},

		addHandler: function(e) {
			var selected = $(this).prevAll('select').find('option:selected').val();
			if (!selected) {AC.Core.Alert.flash('Nothing selected');return;}
			var display = $(this).prevAll('select').find('option:selected').text();

			add(selected, display, this);
		},

		editHandler: function(e) {
			currentCrudAction = this;

			var parent = $(this).parents('div.multiple:eq(0)').attr('data-parent');
			var field = $(this).parents('div.multiple:eq(0)').attr('data-field');
			var editRoute = $(this).parents('div.multiple:eq(0)').attr('data-edit-route');
			var id = $(this).attr('data-id');

			$.fancybox.open({
				href: editRoute + '?edit=' + id + '&hide[' + parent + ']=1&' + getPrefillFields(this) + getHiddenFields(this),
				type: 'iframe',
				autoSize: false,
				maxWidth: 960,
				width: '100%',
				height: '100%',
				closeBtn: false
			});
		},

		editSaved: function(id, display, data) {
			data = data || {};
			var dynamic = (!data.length && $(currentCrudAction).parents('div.multiple:eq(0)').attr('data-dynamic') == 1);
			if (dynamic) {
				getRowData(id, currentCrudAction, function(response) {
					if (!response.data.length) {
						// prevent inf. loop
						response.data = [false];
					}
					AC.Crud.Edit.Multiple.editSaved(id, display, response.data);
				});
			} else {
				$.fancybox.close();
				if (!$(currentCrudAction).is('tr')) {
					$row = $(currentCrudAction).parents('tr:eq(0)');
				} else {
					$row = $(currentCrudAction);
				}
				$row.find('td:eq(1)').text(display);
				var colCounter = 2;
				for (var i in data) {
					$row.find('td:eq(' + colCounter + ')').html(data[i]);
					colCounter++;
				}
				AC.Core.Alert.flash($(currentCrudAction).parents('div.multiple:eq(0)').attr('data-field') + ' edited');
			}
		},

		deleteHandler: function(e) {
			if ($(this).hasClass('btn-danger') && !confirm(i18n.confirmDelete + ' (1 item)')) {return;}

			var that = this;
			submit({
				that:		that,
				operation:	'deleteMultiple',
				params:		{
								id: $(this).attr('data-id')
							}
			}, function(response) {
				if (response.success === true) {
					$(that).parents('tr:eq(0)').fadeOut(function() {
						$(this).remove();
						AC.Crud.Edit.Multiple.initEmptyTable();
					});
					AC.Core.Alert.flash(response.message);
				}
			});
		},
	
		onSortHandler: function(table, row) {
			var form = $(table).parents('form');
			table = $(table);

			form.find('input.operation').val('sort');

			var options = {
				operation	: 'sort',
				crudId		: form.attr('id')
			};
			var url = form.attr('action') + "?" + $.param(options);
			var data = table.tableDnDSerialize();

			// Add CSRF token
			data = data + '&_token=' + form.find('input[name=\'_token\']').val();

			// Add sort table, pk field and fieldname
			data = data + '&table=' + table.find('td.sort:eq(0)').attr('data-table');
			data = data + '&pk=' + table.find('td.sort:eq(0)').attr('data-pk');
			data = data + '&field=' + table.find('td.sort:eq(0)').attr('data-field');

			$.post(url, data, function(response) {
				if (response.operation === 'sort' && response.success === true) {
					AC.Core.Alert.flash(response.message);
				}
			}, 'json').error(function(jqXHR, message, exception) {
				$('body').removeClass('loading');
				errorHandler(i18n.requestError + ' (' + exception + ')');
			});
		}

	};
}();

$(document).ready(function() {
	AC.Crud.Edit.Multiple.init();
});
