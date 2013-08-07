;
$(document).ready(function() {
	$('#forgotform').on('result', function(event, data) {
		if (data.success === false) {
			$('.give-status').addClass('error');
			$('.status-text').text(data.message);
		} else {
			$('body').addClass('loading');
			$('.give-status').removeClass('error');
			$('.status-text').text("User found");
			window.location.href = 'user/logon';
		}
	});
	$('#forgotform').on('error', function(event) {
		$('.give-status').addClass('error');
		$('.status-text').text(i18n.requestError);
	});
	$('#forgotform').on('submit', function(event) {
//		$('.give-status').removeClass('error');
//		$('.status-text').text('Logging in...');
		return true;
	});
	$('#forgotform :input').on('keydown', function() {
		$('.status-text').text('');
	});
});
