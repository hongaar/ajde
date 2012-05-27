;
$(document).ready(function() {
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var redirect = true;
	
	$('form.transactionSetup').bind('result', function(event, data) {
		if (data.success === false) {
			$('dd.status').addClass('error');
			$('dd.status').text(data.message);
			if (typeof redirect === 'function') {
				redirect();
				redirect = true;
			}
		} else {
			if (redirect === true) {
				window.location.href = 'shop/transaction:payment';
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
		$('dd.status').removeClass('error');
		$("dd.status").text('Getting ready for next step...');
		return true;
	});
	
	$('select[name=\'shipment_country\']').on('change', function(e) {
		var self = this;
		redirect = function() {
			$('#shipment').trigger('refresh', {country: $(self).val()});
		};
		$('form.transactionSetup').submit();		
	});
	
});
