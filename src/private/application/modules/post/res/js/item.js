Application.Item = function() {

	var counter = 1;
	var mediaLength;

	return {

		init: function() {

			mediaLength = $('div.portfolio-item div.media').length;

			$('div.portfolio-item a.button.prev').on('click', this.prevMedia);
			$('div.portfolio-item a.button.next').on('click', this.nextMedia);

			if (mediaLength > 1) {
				$('div.portfolio-item a.button.next').fadeIn();
			}

			// Shortcuts
			AC.Shortcut.remove('left');
			AC.Shortcut.remove('right');
			AC.Shortcut.add('left', this.prevMedia); //, { target: $('div.portfolio-item')[0] });
			AC.Shortcut.add('right', this.nextMedia); //, { target: $('div.portfolio-item')[0] });

			// Contact form
			if ($('#getintouch').length) {
				$('#getintouch').submit(AC.Form.Ajax.getHandler);
				$('#getintouch').bind('result', function(e, data) {
					if (data.success === true) {
						$('#getintouch :input').val('');
						AC.Core.Alert.flash('Your message has been sent. Thank you.');
					} else {
						AC.Core.Alert.error('Something went wrong. Please try again.');
					}
				});
			}

			// Focus to receive key events
			$('div.portfolio-item a.button.next').focus();

			// Animate on window resize
			$(window).off('resize.item').on('resize.item', $.throttle(250, Application.Item.animateMedia) );
		},

		nextMedia: function(e) {
			if (counter == mediaLength) return;
			counter++;
			Application.Item.animateMedia();
			if (counter > 1) {
				$('div.portfolio-item a.button.prev').fadeIn();
			}
			if (counter == mediaLength) {
				$('div.portfolio-item a.button.next').fadeOut();
			}
		},

		prevMedia: function(e) {
			if (counter == 1) return;
			counter--;
			Application.Item.animateMedia();
			if (counter == 1) {
				$('div.portfolio-item a.button.prev').fadeOut();
			}
			if (counter < mediaLength) {
				$('div.portfolio-item a.button.next').fadeIn();
			}
		},

		animateMedia: function() {
			var incr = $('div.portfolio-item div.media:first').height();
			var top = (counter - 1) * -1 * incr;
			$('div.portfolio-item div.inner').stop(true).animate({
				top: top + 'px'
			});
		},

		resetMedia: function() {
			var incr = $('div.portfolio-item div.media:first').height();
			var top = (counter - 1) * -1 * incr;
			$('div.portfolio-item div.inner').css({
				top: top + 'px'
			});
		}

	};
}();

$(document).ready(function() {
	Application.Item.init();
});