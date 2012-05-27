;$(document).ready(function() {
	$('input.autoUpdateCrud').click(function() {
		$(this).prev().val($(this).attr('checked') == 'checked' ? 1 : 0);
	});
});