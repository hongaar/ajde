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
			errorHandler('Please click \'apply\' before adding a ' + field);
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
				addRow(id, display, that);
				AC.Core.Alert.flash(response.message);
			} else if (response.success === false) {
				AC.Core.Alert.error(response.message);
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
	}
	
	var addRow = function(id, display, that, data) {
		data = data || {};
		var dynamic = (!data.length && $(that).parents('div.multiple:eq(0)').attr('data-dynamic') == 1);
		if (dynamic) {
			getRowData(id, that, function(response) {
				addRow(id, display, that, response.data);
			});
		} else {
			$lastrow = $(that).parent().next().find('tbody tr:last');
			$newrow = $lastrow.clone();				
			$newrow.find('td:eq(0)').text(id);
			$newrow.find('td:eq(1)').text(display);
			var colCounter = 2;
			for (var i in data) {
				$newrow.find('td:eq(' + colCounter + ')').html(data[i]);
				colCounter++;
			}
			$newrow.find('td:eq(' + colCounter + ')').find('a').attr('data-id', id);
			$newrow.find('td:eq(' + colCounter + ')').find('a:eq(1)').text($(that).parent().find('.addMultiple').length ? 'disconnect' : 'delete');
			$newrow.hide();
			$newrow.insertAfter($lastrow);
			$newrow.fadeIn();
		}
	};
	
	return {
		
		init: function() {
			$('form.ACCrudEdit div.multiple a.newMultiple').click(AC.Crud.Edit.Multiple.newHandler);
			$('form.ACCrudEdit div.multiple a.addMultiple').click(AC.Crud.Edit.Multiple.addHandler);
			$('form.ACCrudEdit div.multiple a.editMultiple').live('click', AC.Crud.Edit.Multiple.editHandler);
			$('form.ACCrudEdit div.multiple tbody tr').live('dblclick', AC.Crud.Edit.Multiple.editHandler);
			$('form.ACCrudEdit div.multiple a.deleteMultiple').live('click', AC.Crud.Edit.Multiple.deleteHandler);
			
			$('form.ACCrudEdit div.multiple a.imagePreview').fancybox();
		},
		
		newHandler: function(e) {
			var parentId = $(this).parents('div.multiple:eq(0)').attr('data-parent-id');
			var field = $(this).parents('div.multiple:eq(0)').attr('data-field');
			if (!parentId) {
				errorHandler('Please click \'apply\' before adding a ' + field);
				return;
			}
			
			currentCrudAction = this;
			
			var parent = $(this).parents('div.multiple:eq(0)').attr('data-parent');			
			var editRoute = $(this).parents('div.multiple:eq(0)').attr('data-edit-route');
			
			$.fancybox.open({
				href: editRoute + '?new&prefill[' + parent + ']=' + parentId + '&disable[' + parent + ']=1',
				type: 'iframe',
				autoSize: false,				
				maxWidth: 800,
				width: '100%',
				height: '100%'
			});
		},
		
		newSaved: function(id, display) {
			if ($(currentCrudAction).nextAll('.addMultiple').length) {
				$.fancybox.close();
				add(id, display, currentCrudAction);
			} else {				
				$.fancybox.close();
				addRow(id, display, currentCrudAction);				
				AC.Core.Alert.flash($(currentCrudAction).parents('div.multiple:eq(0)').attr('data-field') + ' added');
			}
		},
		
		addHandler: function(e) {			
			var selected = $(this).prev().find('option:selected').val();
			if (!selected) {AC.Core.Alert.flash('Nothing selected');return;}
			var display = $(this).prev().find('option:selected').text();
			
			add(selected, display, this);
		},
		
		editHandler: function(e) {			
			currentCrudAction = this;
			
			var parent = $(this).parents('div.multiple:eq(0)').attr('data-parent');
			var field = $(this).parents('div.multiple:eq(0)').attr('data-field');
			var editRoute = $(this).parents('div.multiple:eq(0)').attr('data-edit-route');
			var id = $(this).attr('data-id');
			
			$.fancybox.open({
				href: editRoute + '?edit=' + id + '&disable[' + parent + ']=1',
				type: 'iframe',
				autoSize: false,				
				maxWidth: 800,
				width: '100%',
				height: '100%'
			});
		},
		
		editSaved: function(id, display, data) {			
			data = data || {};
			var dynamic = (!data.length && $(currentCrudAction).parents('div.multiple:eq(0)').attr('data-dynamic') == 1);
			if (dynamic) {
				getRowData(id, currentCrudAction, function(response) {
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
			if ($(this).text().trim() === 'delete' && !confirm(i18n.confirmDelete + ' (1 item)')) {return;}
			
			var that = this;
			submit({
				that:		that,
				operation:	'deleteMultiple',
				params:		{
								id: $(this).attr('data-id')
							}
			}, function(response) {
				if (response.success === true) {
					$(that).parents('tr:eq(0)').css({backgroundColor:'red'}).fadeOut(function() {
						$(this).remove();
					});
					AC.Core.Alert.flash(response.message);
				}
			});
		}
		
	};
}();

$(document).ready(function() {
	AC.Crud.Edit.Multiple.init();
});