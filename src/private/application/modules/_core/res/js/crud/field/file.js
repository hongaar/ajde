;$(document).ready(function() {
		
	var updateChooseThis = function() {
		var that = this;
		$('div.fancybox-title a').unbind('click').click(function() {
			$(that.element).parents('a:eq(0)').click();
			$.fancybox.close();
		});
	};
	
	$('div.browser > a span.imagePreview').fancybox({
		beforeLoad: function() {
			this.title = this.title + ' &gt; <a href=\'javascript:void(null);\' style=\'color: white\'>choose this</a>'
		},
		afterLoad: updateChooseThis,
		afterShow: updateChooseThis
	});
	$('div.filelist a.imagePreview').fancybox();
	
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
		$browser = $(this).next();
		$browser.slideToggle();
		if ($(this).parents('.block.sidebar').length && $browser.height() < 100) {
			$(this).parent().css({height: '550px'});
		} else if ($(this).parents('.block.sidebar').lengths) {
			$(this).parent().css({height: 'auto'});
		}
	});
	
	$('a.toggleFileBrowser').toggle(function() {
		$(this).text('Close file browser');
	}, function() {
		$(this).text('Choose existing');
	})
	
	$('div.browser > a').click(function(e) {
		// Fired from preview link?
		if ($(e.srcElement).hasClass('preview')) { return; }
		e.preventDefault();
		$(this).parent().find('a').removeClass('active');
		$(this).addClass('active');
		var filename = $(this).find('span.filename').text();
		$(this).parents('div.fileupload:eq(0)').find('input').val(filename);
	});
	
	$('div.browser > a span.filePreview').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		window.open($(this).attr('href'), 'preview');		
	})
});