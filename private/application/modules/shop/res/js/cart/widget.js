;
$(document).ready(function() {
	
	var infoHandler		= alert;
	var warningHandler	= alert;
	var errorHandler	= alert;
	
	$('#cart_widget').bind('refresh', function(event, data) {
		var $div = $(this);

		$div.animate({
			opacity: 0
		}, 'fast');
		
		var url = 'shop/cart:widget/body';
		if ($div.attr('data-quickcheckout') == '1') {
			url = url + '/quickcheckout';
		}

		$.get(url, data, function(response) {
			$div.html($($(response)[0]).html());
			$div.stop().css({opacity: 1});
		}, 'html').error(function(jqXHR, message, exception) {
			$('body').removeClass('loading');
			errorHandler(i18n.requestError + ' (' + exception + ')');
		});
	});
	
});
