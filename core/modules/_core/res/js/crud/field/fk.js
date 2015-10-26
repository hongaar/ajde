;
if (typeof AC === "undefined") {
    AC = function () {
    }
}
if (typeof AC.Crud === "undefined") {
    AC.Crud = function () {
    }
}
if (typeof AC.Crud.Edit === "undefined") {
    AC.Crud.Edit = function () {
    }
}
AC.Crud.Edit.Fk = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;

	var getRowData = function(id, that, callback) {
		var form = $(that).parents('form');
		var field = $(that).attr('data-field');
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

	return {

		init: function() {
			if ($.fn.fancybox) {
				$('form.ACCrudEdit div.picker.fk a.imagePreview').fancybox({closeBtn: false});
			}
			$('form.ACCrudEdit div.picker.fk').bind('chosen', this.chosenHandler);
		},

		chosenHandler: function(e, ids) {
			var that = this;
			var $select = $(that).parents('.control-group').find('select[name]');

			$(that).find('div.input').removeClass('with-image');

			if (!ids || !ids.length) {
				$select.val('').trigger('change');
				if ($(that).data('use-image') == '1') {
					$(that).find('div.input').html('<input type="text" readonly="readonly" value="Please choose one" />');
				} else {
					$(that).find('input').val('Please choose one');
				}
			} else {
				for (elm in ids) {
					var id = ids[elm];
					if ($(that).data('use-image') == '1') {
						$(that).find('div.input').html('<i class="icon-loading"></i>');
					} else {
						$(that).find('input').val('loading...');
					}
					getRowData(id, that, function(response) {
						var image = response.data[0];
						var display = response.displayField;

						// set selection to new id
						$select.val(id);

						// new item for select element?
						if ($select.val() != id) {
							$select.append('<option value=' + id + '>' + display + '</option>');
							$select.val(id).trigger('change');
						} else {
                            $select.trigger('change');
                        }

						// update display
						if ($(that).data('use-image') == '1') {
							$(that).find('div.input').addClass('with-image');
							$(that).find('div.input').html(image);
						} else {
							$(that).find('input').val(display);
						}
					});
				}
			}
		}

	};
}();

$(document).ready(function() {
	AC.Crud.Edit.Fk.init();
});
