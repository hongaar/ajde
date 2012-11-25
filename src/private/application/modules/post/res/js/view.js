;
Application.Portfolio = function() {

	var $canvas;
	var $portfolioView;
	var $portfolioFilter;

	var scrollbarTimer;
	var horizontalScrollWidth;
	var $pane;
	var $scrollbar;
	var $drag;

	var origTitle;

	return {

		init: function() {

			// Preload
			$canvas = $('div.portfolio-view .inner');
			$portfolioView = $('div.portfolio-view');
			$portfolioFilter = $('div.portfolio-filter');

			origTitle = document.title;

			// Resize items?
			this.resizeItems();

			// Set minimum size of .inner to force scrollbars
			this.setMinWidth();

			if (this.doScrollPane()) {
				// Custom scrollbar
				this.scrollPane();

				// Scroll portolio-view
				this.scrollPaneMousewheel();

				// Hover tags
				this.hoverFilter();
			}

			// Resize portfolio-view
			this.resizePortfolioView();

			// Resize portfolio-filter
			this.resizePortfolioFilter();

			// Detect touch
			this.setTouchClass();

			// Reset origin on canvas
			this.resetTranslation();

			// Draggable portfolio
			if (this.doScrollPane()) {
				this.setupDrag();
			}

			// Action for click on item
			this.itemHandler();

			// Click on ajax-wrapper
			this.wrapperHandler();

			// Listen to navigation complete
			this.navigationCompleteHandler();

			// Set resizing class on body
			this.resizeClass();

			// Trigger resize now
			$(window).trigger('resize.portfolioView');
		},

		navigationCompleteHandler: function() {
			$('body').on('navigationClose', function() {
				document.title = origTitle;
				if (Application.Portfolio.doZoom()) {
					Application.Navigation.setAnimation(false);
					Application.Portfolio.zoomOut();
				} else {
					Application.Navigation.setAnimation(true);
					// Zoomout anyway, because window could have been resized in the meantime
					Application.Portfolio.zoomOut();
				}
			});
		},

		wrapperHandler: function() {
			$('body').on('navigationComplete', function() {
				$('#ajax-wrapper').click(function(e) {
					if ($(e.srcElement).attr('id') == 'ajax-wrapper') {
						Application.Navigation.closeHandler(e);
					}
				});
			});
		},

		setupDrag: function() {

			$pane.draggable({
				axis: "x",
				drag: function(e, ui) {
					Application.Portfolio.showScrollbar();
				},
				start: function(e, ui) {
					$portfolioView.addClass('noclick');
				},
				stop: function(e, ui) {
					// reset cursor
					$('body').css({ cursor: '' });

					Application.Portfolio.fixScrollOverflow();

					setTimeout(function() {
						$portfolioView.removeClass('noclick');
					}, 300);
				}
			});
		},

		fixScrollOverflow: function() {
			if ($pane.position().left > 0) {
				$pane.stop(true).animate({left: 0});
			}

			if ($pane.position().left < ($portfolioView.width() - $pane[0].scrollWidth)) {
				$pane.stop(true).animate({left: Math.min(0, ($portfolioView.width() - $pane[0].scrollWidth)) + 'px'});
			}
		},

		itemHandler: function() {
			$('div.portfolio-view a.item').click(function(e) {
				// Prevent Navigation.clickHandler()
				e.preventDefault();
				e.stopPropagation();

				// Prevent click event while dragging
				if ($portfolioView.hasClass('noclick')) return;

				// Preloading
				var $this = $(this);

				// Do the optional animation and AJAX request
				if (Application.Portfolio.doZoom()) {
					// Show overlay at zoom complete
					Application.Portfolio.zoomIn($this, function() {}, 500);
					Application.Navigation.setAnimation(false);
					Application.Navigation.clickHandler.call($this, e, 500);
				} else {
					Application.Navigation.setAnimation(true);
					Application.Navigation.clickHandler.call($this, e);
				}

			});
		},

		resetTranslation: function() {
			$canvas.transform({
				translate: [0, 0],
				origin: [0, 0],
				scale: [1, 1]
			});
		},

		doScrollPane: function() {
			return !Application.Client.isMobile();
		},

		resizeItems: function() {
			if ($(window).width() < 768) { // || Application.Client.isMobile()) {
				// var factor = Application.Client.isMobile() ? 1.7 : 1.3;
				var factor = ($(window).width() < 420) ? 1.7 : 1.3;
				$('.portfolio-view a.item').each(function() { $(this).css({width:$(this).width()/factor+'px'}); });
				$('.portfolio-view a.item').each(function() { $(this).css({left:$(this).position().left/factor+'px'}); });
			}
		},

		setMinWidth: function() {
			// To force scrollbar, instead hacking on jScrollPane
			// $canvas.css({minWidth: ''});
			// $canvas.css({minWidth: Math.max($canvas[0].scrollWidth + 1, $canvas.width() + 1) + 'px'});
		},

		setTouchClass: function() {
			if ('ontouchstart' in document.documentElement || !!('ontouchstart' in window) || window.Touch) {
				$portfolioView.addClass('touch');
			}
		},

		hoverFilter: function() {
			$portfolioFilter.hover(function(e) {
				$portfolioView.addClass('matching');
			}, function(e) {
				if ($portfolioView.hasClass('fixed')) return;
				$portfolioView.removeClass('matching');
			});

			$('div.portfolio-filter ul.tags a').hover(function(e) {
				$portfolioView.find("a[data-tags*='#" + $(this).data('id') + "#']").addClass('match');
			}, function(e) {
				if ($(this).hasClass('fixed')) return;
				$portfolioView.find("a[data-tags*='#" + $(this).data('id') + "#']").removeClass('match');
				reapply();
			}).click(function(e) {
				e.preventDefault();
				$this = $(this);
				if ($this.hasClass('fixed')) {
					$this.removeClass('fixed');
					$portfolioView.find("a[data-tags*='#" + $this.data('id') + "#']").removeClass('match');
					if (!$this.closest('ul').find('a.fixed').length) {
						$portfolioView.removeClass('fixed');
						$portfolioFilter.removeClass('fixed');
					} else {
						reapply();
					}
				} else {
					$this.addClass('fixed');
					$portfolioView.addClass('fixed');
					$portfolioFilter.addClass('fixed');
					$portfolioView.find("a[data-tags*='#" + $this.data('id') + "#']").addClass('match');
				}
			});

			var reapply = function() {
				$portfolioFilter.find('a.fixed').each(function() {
					$portfolioView.find("a[data-tags*='#" + $(this).data('id') + "#']").addClass('match');
				});
			};
		},

		doZoom: function() {
			// no-zoom scenarios
			return !(
				$(window).width() < 768 || // small screens
				(Application.Client.deviceOs() === 'win' && Application.Client.deviceVersion() < 6.1) || // pre windows 7
				Application.Client.isMobile()
			);
		},

		forceRedraw: function(elm) {
			var n = document.createTextNode(' ');
			elm[0].appendChild(n);
			setTimeout(function() { n.parentNode.removeChild(n); }, 0);
			return elm;
		},

		resizePortfolioView: function() {
			var jsp = $portfolioView.data('jsp');

			var $window = $(window);
			var $container = $portfolioView.find('.jspContainer');

			var resizeFunction = function() {
				var margin = $('header').height();

				if (margin > 20) {
					// we got a header, add padding
					 margin += 20;
				}

				// Set height of view
				$portfolioView.css({height: $window.height() - margin + 'px'});

				// Set minimum width of .inner to force scrollbars
				Application.Portfolio.setMinWidth();

				if (typeof jsp !== 'undefined') {
					Application.Portfolio.scrollPane();
				}
			};

			$window.on('resize.portfolioView', $.throttle(250, resizeFunction) );
		},

		resizePortfolioFilter: function() {
			var $window = $(window);
			$window.on('resize.portfolioView', $.throttle(250, function() {
				$portfolioFilter.css({
					height: $window.height() - 260 + 'px'
				});
			}) );
		},

		resizeClass: function() {
			$(window).on('resize.portfolioView', function() {
				$('body').addClass('resizing');
			}).on('resize.portfolioView', $.debounce(250, function() {
				$('body').removeClass('resizing');
			}) );
		},

		scrollPane: function() {
			if (typeof $portfolioView.data('jsp') === 'undefined') {
				$portfolioView.jScrollPane({
					showArrows: true,
					verticalDragMinHeight: 40,
					verticalDragMaxHeight: 40,
					horizontalDragMinWidth: 40,
					horizontalDragMaxWidth: 40,
					alwaysShowScroll: { vertical: false, horizontal: true }
				});
			} else {
				$portfolioView.data('jsp').reinitialise();
			}

			// Preload scroll elements
			$scrollbar = $('.jspHorizontalBar');
			$drag = $('.jspDrag');
			$pane = $('div.portfolio-view .jspPane');
			horizontalScrollWidth = $('.jspContainer').innerWidth() - 120;
		},

		scrollPaneMousewheel: function() {
			var jsp = $portfolioView.data('jsp');

			$('div.portfolio-view .jspPane').mousewheel(function(e, delta) {
				if (jsp.getIsScrollableH()) {
					$pane.css({left: '+=' + (delta * 60) + 'px'});
					Application.Portfolio.showScrollbar();
				}
				e.preventDefault();
			});

			$('div.portfolio-filter').mousewheel(function(e, delta) {
				if (jsp.getIsScrollableH()) {
					$pane.css({left: '+=' + (delta * 60) + 'px'});
					Application.Portfolio.fixScrollOverflow();
					Application.Portfolio.showScrollbar();
				}
				e.preventDefault();
			});
		},

		showScrollbar: function() {
			if (!$scrollbar.lenght) {
				$scrollbar = $('.jspHorizontalBar');
			}
			$scrollbar.css({opacity: 1});
			var current = $portfolioView.data('jsp').getPercentScrolledX() * horizontalScrollWidth;
			$drag.css({left: Math.min(horizontalScrollWidth, Math.max(0, current)) + 'px'});

			clearTimeout(scrollbarTimer);
			scrollbarTimer = setTimeout(function() {
				$scrollbar.css({opacity: ''});
			}, 500);
		},

		zoomOut: function(callback, timeout) {
			// remove class
			$('div.portfolio-view').addClass('zooming-out');

			if (UMB.getCurrentBrowser() === 'ie') {
				// Use animate
				$canvas.animate({
					translate: [0, 0],
					origin: [0, 0],
					scale: [1, 1]
				}, 750);
			} else {
				Application.Portfolio.resetTranslation();
			}

			// wait for for transition to complete
			setTimeout(function() {
				$('div.portfolio-view').removeClass('zooming-out');
				$('div.portfolio-view').removeClass('zoomed-in');
				$('div.portfolio-view a.item').removeClass('target');
				if (typeof callback === 'function') {
					callback();
				}
			}, timeout || 750);
		},

		zoomIn: function($item, callback, timeout) {
			// set class
			$('div.portfolio-view').addClass('zooming-in');
			$item.addClass('target');

			// current offset
			var origin = {
				top: $item.position().top,
				left: $item.position().left,
				width: $item.width() - 20,
				height: $item.height() - 20
			};

			// jspPane offset
			var scrollLeft = $('div.portfolio-view .jspPane').position().left + $canvas.position().left;

			// determine is the target position
			var canvasWidth = $('div.container .row').width();
			var targetWidth = Math.min(750, canvasWidth);
			var target = {
				top: 0,
				left: (canvasWidth - targetWidth) / 2,
				width: targetWidth,
				height: ($(window).height() < 851) ? 350 : 562
			};

			// determine image fitting & scaling
			var scaleOffset = { top: 0, left: 0 };

			// image fits width
			var scale = target.width / origin.width;
			var imageWidth = target.width;
			var imageHeight = scale * origin.height;
			scaleOffset.top = (target.height - imageHeight) / 2;

			if (imageHeight > target.height) {
				scale = target.height / origin.height;
				imageHeight = target.height;
				imageWidth = scale * origin.width;
				scaleOffset.top = 0;
				scaleOffset.left = (target.width - imageWidth) / 2;
			}

			// transform translation
			var correction = 5;

			var translation = {
				left: target.left - ((origin.left + correction) * scale) + scaleOffset.left - scrollLeft,
				top: target.top - ((origin.top + correction) * scale) + scaleOffset.top
			};

			var css ={
				origin: [ 0, 0 ],
				translate: [ translation.left + 'px', translation.top + 'px' ],
				scale: [ scale, scale ]
			};
			if (UMB.getCurrentBrowser() === 'ie') {
				// Use animate
				$canvas.animate(css, 500);
			} else {
				// Use CSS transition
				$canvas.transform(css);
			}

			// wait for for transition to complete
			setTimeout(function() {
				$('div.portfolio-view').removeClass('zooming-in');
				$('div.portfolio-view').addClass('zoomed-in');
				if (typeof callback === 'function') {
					callback();
				}
			}, timeout || 2000);
		}

	};
}();

$(document).ready(function() {
	Application.Portfolio.init();
});