;
$(document).ready(function() {	
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	$('form.editCart').on('click', 'a.delete', function(e) {
		e.preventDefault();
		$this = $(this);
		$this.parents('tr:first').find('input.qty').val(0);
		$this.parents('form:first').submit();
	});
	
	$('form.editCart').on('blur', 'input.qty', function(e) {
		$this = $(this);
		$this.parents('form:first').submit();
	});
	
	$('form.editCart').bind('result', function(e, response) {
		$(this).html($(response).filter('form.editCart').html());
	});
	
	$('form.editCart').bind('error', function(event, jqXHR, message, exception) {
		errorHandler(i18n.requestError);
	});
	
});
