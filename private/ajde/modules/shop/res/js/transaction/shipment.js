;
$(document).ready(function() {
	
	var infoHandler		= alert;
	var warningHandler	= alert;
	var errorHandler	= alert;
	
	$('#shipment').bind('refresh', function(event, data) {
		var $div = $(this);

		$div.animate({
			opacity: 0
		}, 'fast');
		
		var url = 'shop/transaction:shipment/body';
		
		$.get(url, data, function(response) {
			$div.html(response);
			$div.stop().css({opacity: 1});
		}, 'html').error(function(jqXHR, message, exception) {
			$('body').removeClass('loading');
			errorHandler(i18n.requestError + ' (' + exception + ')');
		});
	});
	
});
