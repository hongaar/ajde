;
$(document).ready(function() {
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	$('form.addToCart').bind('result', function(event, data) {
		if (data.success === false) {
			errorHandler(data.message);
		} else {
			$('#cart_widget').trigger('refresh', {});
			AC.Core.Alert.flash('Item is now added to your shopping cart');			
		}
	});
	$('form.addToCart').bind('error', function(event, jqXHR, message, exception) {
		errorHandler(i18n.requestError);
	});
	
});
