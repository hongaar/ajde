;
if (typeof AC ==="undefined") {AC = function() {}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function() {}};

AC.Crud.Mainfilter = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;

	return {

		init: function() {
			$('#mainFilter a.filter').live('click', AC.Crud.Mainfilter.mainFilterHandler);
			$('#mainFilter button.all').live('click', AC.Crud.Mainfilter.allHandler);
			var current = $('#mainFilter .mainFilterButtons a[data-init]').text();
			if (current) {
				$('.mainFilterLabel').text(current);
			} else if ($('#mainFilter').data('autoopen') && $('#mainFilter a.filter').length) {
				$('#mainFilter').modal('show');
			}
		
			// Prevent submit
			var form = $('form.ACCrudEdit');
			form.data('onBeforeSubmit', function() {
				var name = $('#mainFilter a').data('name');
				var current = form.find(':input[name="' + name + '"]').val();
				if (!current) {
					errorHandler('Please choose a ' + name);
					return false;
				}
			});
		},
				
		allHandler: function(e) {
			var name = $(this).data('name');
			var form = $('form.ACCrud');
			var curVal = form.filter('.ACCrudList').find('select[name="view[filter][' + name + ']"]').val();
			if (curVal) {
				form.filter('.ACCrudList').find('select[name="view[filter][' + name + ']"]').val('');
				AC.Crud.List.updateView(form.children(':eq(0)'));
			}
			$('#mainFilter a').addClass('btn-info');
			$('.mainFilterLabel').text('Filter');
			$('#mainFilter').modal('hide');
		},

		mainFilterHandler: function(e) {
			var name = $(this).data('name');
			var value = $(this).data('value');
			var form = $('form.ACCrud');
			
			$('#mainFilter a').addClass('btn-info');
			$(this).removeClass('btn-info');
			
			// for list
			form.filter('.ACCrudList').find('select[name="view[filter][' + name + ']"]').val(value);
			
			// for edit
			form.filter('.ACCrudEdit').find(':input[name="' + name + '"]').val(value);
			
			$('#mainFilter').modal('hide');
			$('.mainFilterLabel').text($(this).text());
			
			if (AC.Crud.List) {
				AC.Crud.List.updateView(form.children(':eq(0)'));
			} else {
				// Call dynamic fields update
				AC.Crud.Edit.dynamicFields.call(form.filter('.ACCrudEdit').find(':input[name="' + name + '"]'));
			
				// Refresh?
//				if ($('#mainFilter').data('refresh') == 1) {
//					window.location.reload(true);
//				}
				
				// Do update the session
//				var data = {};
//				data['view[filter][' + name + ']'] = value;
//				var url = document.location.href;
//				$.get(url, data, function(response) {
//					// no feedback
//				}, 'html');
			}
		}

	};
}();

$(document).ready(function() {
	AC.Crud.Mainfilter.init();
});
