;$(document).ready(function() {
	
	// Lazyload
    var i = 0;
    $('div.browser').each(function() {
        i++;
        var $browser = $(this);
        $browser.attr('id', 'browser' + i);
        $browser.find('img').lazyload({
            container: $('#browser' + i)
        });
    });
	
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
		closeBtn: false,
		afterLoad: updateChooseThis,
		afterShow: updateChooseThis,
		helpers: {
			thumbs: {
				width	: 50,
				height	: 50,
				source	: function(item) {
					return $(item.element).parent().prev().attr('src');
				}
			}
		}
	});
	$('div.filelist a.imagePreview').fancybox({closeBtn: false});
	
	$('a.deleteFileCrud').click(function(e) {
		e.preventDefault();
		$filelist = $(this).parents('div.filelist');
		$fileupload = $filelist.prev();
		$filelist.remove();
		$fileupload.removeClass('hidden');
        $fileupload.find('input[type=hidden]').trigger('change');
		return false;
	});
	
	$('a.toggleFileBrowser').click(function(e) {
		e.preventDefault();
		$browser = $(this).parent().next();
		var that = this;
        setTimeout(function() {
            $browser.css('margin-left', '');
            var diff = $(window).width() - ($browser.width() + $browser.offset().left);
            if (diff < 0) {
                $browser.css('margin-left', diff - 25 + 'px');
            } else {
                $browser.css('margin-left', '');
            }
        }, 0);
		$browser.slideToggle(function() {
			$(this).trigger('scroll');
			if ($(that).parents('.block.sidebar').length && $browser.is(':visible')) {
				$(that).parent().css({height: '550px'});
			} else if ($(that).parents('.block.sidebar').length) {
				$(that).parent().css({height: 'auto'});
			}
		});		
	});
	
	$('a.toggleFileBrowser').toggle(function() {
		$(this).html('<i class="icon-remove"></i> close file browser');
        $('fieldset.crud').addClass('notransition');
	}, function() {
		$(this).html('<i class="icon-search"></i> choose existing');
        $('fieldset.crud').removeClass('notransition');
	});

    $('div.browser input.search').bind('input', function(e) {
        var q = $(this).val().toLowerCase();
        var $browser = $(this).parents('.browser');
        if (q == '') {
            $browser.find('> a').show();
        } else {
            $browser.find('> a').each(function () {
                if ($(this).find('span.filename').text().toLowerCase().indexOf(q) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
        $browser.trigger('scroll');
    });
	
	$('div.browser > a').click(function(e) {
		// Fired from preview link?
		if ($(e.srcElement).hasClass('preview')) { return; }
		e.preventDefault();
		$(this).parent().find('a').removeClass('active');
		$(this).addClass('active');
		var filename = $(this).find('span.filename').text();
		$(this).parents('div.fileupload:eq(0)').find('input[type=hidden]').val(filename).trigger('change');
        $(this).parents('.filebrowser').find('a.toggleFileBrowser').addClass('btn-success')
	});
	
	$('div.browser > a span.filePreview').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		window.open($(this).attr('href'), 'preview');		
	})
});