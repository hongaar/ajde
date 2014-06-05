;$(document).ready(function() {
	$('a.picker-icon').click(function() {
		$(this).prevAll('input').val( $(this).data('value')).trigger('change');
		$(this).parent().children('a').removeClass('active');
		$(this).addClass('active');
	});
});