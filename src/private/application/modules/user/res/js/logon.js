;
$(document).ready(function() {
	$('#loginform').bind('result', function(event, data) {
		if (data.success === false) {
			$('dd.status').addClass('error');
			$("dd.status").text(data.message);
		} else {
			if ($("#returnto").val()) {
				$('body').addClass('loading');
				window.location.href = $("#returnto").val();
			} else {
				$('body').addClass('loading');
				window.location.reload(true);
			}
		}
	});
	$('#loginform').bind('error', function(event) {
		$('dd.status').addClass('error');
		$("dd.status").text(i18n.requestError);
	});
	$('#loginform').bind('submit', function(event) {
		$('dd.status').removeClass('error');
		$("dd.status").text("Logging in...");
		return true;
	});
});
