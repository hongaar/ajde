;
$(document).ready(function() {
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var redirect = true;

	$('form.transactionSetup').bind('result', function(event, data) {
		if (data.success === false) {
			$('.status').addClass('error');
			$('.status').text(data.message);
			if (typeof redirect === 'function') {
				redirect();
				redirect = true;
			}
		} else {
			if (redirect === true) {
				window.location.href = $('base').attr('href') + 'shop/transaction:payment';
			} else if (typeof redirect === 'function') {
				redirect();
				redirect = true;
			}
		}
	});
	$('form.transactionSetup').bind('error', function(event, jqXHR, message, exception) {
		errorHandler(i18n.requestError);
	});
	$('form.transactionSetup').bind('submit', function(event) {
		saveToLeave = true;
		$('.status').removeClass('error');
		$('.status').text(i18n.shopNextStep);
		return true;
	});

	$(':input[name=shipment_country]').on('change', function(e) {
		var self = this;
		redirect = function() {
			$('#shipment').trigger('refresh', {country: $(self).val()});
		};
		$('form.transactionSetup').submit();
	});
    //setTimeout(function() {
    //    $(':input[name=shipment_country]').trigger('change');
    //}, 0);
	
});
