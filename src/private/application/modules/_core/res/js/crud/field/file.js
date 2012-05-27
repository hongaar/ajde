;$(document).ready(function() {
	$('a.deleteFileCrud').click(function(e) {
		e.preventDefault();
		$filelist = $(this).parents('div.filelist');
		$fileupload = $filelist.prev();
		$filelist.remove();
		$fileupload.removeClass('visuallyhidden');
		return false;
	});
	
	$('a.toggleFileBrowser').click(function(e) {
		e.preventDefault();
		$(this).next().slideToggle();
	});
	
	$('div.browser > a').click(function(e) {
		e.preventDefault();
		$(this).parent().find('a').removeClass('active');
		$(this).addClass('active');
		var filename = $(this).find('span.filename').text();
		$(this).parents('div.fileupload:eq(0)').find('input').val(filename);
	});
	
	$('div.browser > a span.preview').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		window.open($(this).attr('data-url'), 'preview');
	})
});