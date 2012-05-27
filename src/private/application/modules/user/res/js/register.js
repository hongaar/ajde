;
$(document).ready(function() {
	$('#registerform').bind('result', function(event, data) {
		if (data.success === false) {
			$('dd.status').addClass('error');
			$("dd.status").text(data.message);
		} else {
			if (data.returnto !== false) {
				window.location.href = data.returnto;
			} else {
				window.location.href = 'user';
			}
		}
	});
	$('#registerform').bind('error', function(event) {
		$('dd.status').addClass('error');
		$("dd.status").text('Something went wrong');
	});
	$('#registerform').bind('submit', function(event) {
		$('dd.status').removeClass('error');
		$("dd.status").text('Registering...');
		return true;
	});
});
