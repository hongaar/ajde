;
$(document).ready(function() {
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	$('form.transactionPayment').bind('result', function(event, data) {
		if (data.success === false) {
			$('.status').addClass('error');
			$('.status').text(data.message);
			AC.Core.Alert.hide();
		} else {
			if (data.postproxy || data.redirect) {
				AC.Core.Alert.show(i18n.shopRedirectingPaymentProvider);
                $('.status').text(i18n.shopRedirectingPaymentProvider);
			}
			
			if (data.postproxy) {			
				$('#postproxy').html(data.postproxy);
				$('#postproxy form:eq(0)').submit();
			} else if (data.redirect) {
                window.location.href = data.redirect;
			} else {
				AC.Core.Alert.error(i18n.applicationError);
			}
		}
	});
	$('form.transactionPayment').bind('error', function(event, jqXHR, message, exception) {
		errorHandler(i18n.requestError);
	});
	$('form.transactionPayment').bind('submit', function(event) {
		$('.status').removeClass('error');
		$(".status").text(i18n.shopNextStep);
		return true;
	});

    $('form.transactionPayment .provider label').on('click', function(e) {
        $('form.transactionPayment .provider label').removeClass('active');
        $(this).addClass('active');
    });
	
});
