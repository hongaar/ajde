;$(document).ready(function() {
	$('input.autoUpdateCrud').click(function() {
		update(this);
	});
	
	$('input.autoUpdateCrud + .picker-handle').click(function() {
		update($(this).prev());
	});
	
	var update = function(input) {
		setTimeout(function() {
			$(input).parents('div.controls').find('input[type=hidden]').val($(input).attr('checked') == 'checked' ? 1 : 0).change();
			$(input).next().children('label').text($(input).attr('checked') == 'checked' ? 'On' : 'Off');
		}, 0);
	}
});