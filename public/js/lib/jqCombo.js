(function($, undefined) {
	"use strict";
	
	/**
	* @version 0.1
	* @author Joram van den Boezem
	* @source https://github.com/hongaar/jqCombo
	* 
	* Simple jQuery plugin to create combobox / autocomplete functionality
	* 
	* Only a very lightweight plugin which uses a native browser `INPUT` element,
	* no custom panels or jQuery UI stuff.
	* 
	* However, please note that this plugin requires some browser sniffing and
	* tricky positioning of the `INPUT` element over the `SELECT` element. Using
	* this plugin on styled `SELECT` elements might not work as expected.
	* 
	* Tested with: Chrome 20, IE7+, Firefox 13, Safari 5.1, Opera 12.
	* On IE6 and mobile devices it will just show the `SELECT` element
	* 	
	* 
	* 
	* UNLICENSE:
	* 
	* This is free and unencumbered software released into the public domain.
	* 
	* Anyone is free to copy, modify, publish, use, compile, sell, or
	* distribute this software, either in source code form or as a compiled
	* binary, for any purpose, commercial or non-commercial, and by any
	* means.
	* 
	* In jurisdictions that recognize copyright laws, the author or authors
	* of this software dedicate any and all copyright interest in the
	* software to the public domain. We make this dedication for the benefit
	* of the public at large and to the detriment of our heirs and
	* successors. We intend this dedication to be an overt act of
	* relinquishment in perpetuity of all present and future rights to this
	* software under copyright law.
	* 
	* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	* EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	* MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
	* IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
	* OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
	* ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
	* OTHER DEALINGS IN THE SOFTWARE.
	* 
	* For more information, please refer to <http://unlicense.org/>
	* 
	*/
	
	/**
	 * Global variables
	 */
	var defaults = {
		 expandOnFocus	: true,
		 expandSize		: 10,
		 notfoundCss	: {color: 'red'}
	};
	
	var keys = {
		ignore: [			
			35, // end
			36, // home
			37, // left
			39 // right			
		],
		up: [
			38 // up
		],
		down: [
			40 // down
		],
		enter: [
			13 // enter
		],
		lookback: [
			16, // shift
			17, // ctrl
			18 // alt
		],
		noselection: [
			8, // backspace
			46 // delete
		]
	};
	
	var keypressCounter = 0;
	
	// Object of plugin methods	
	var methods = {
		init: function(o) {
			/**
			 * Instance variables
			 */						
			var _options = $.extend({}, defaults, o);
						
			// Ignore mobile devices
			if (_isMobile().any) {
				return false;
			}
			
			// Clear elements
			_cleanup(this);			
			
			// The Loop
			return this.each(function() {
				var $select = $(this);
				
				// Adds jqcombo class to $select
				$select.addClass('jqcombo');
				
				// Adds the textbox
				var $input = _inputAfter($select);
				
				// Position relative to $select
				_positionInput($select, $input);
				
				// Disable tab on select elements
				_disableTabstop($select);
				
				// Watch the select for changes and update input right away
				_watchChanges($select, $input);
				
				// Keypress counter and repeater neutralizer
				_initKeypressCounter($input);
				
				// Autocompletes the input when typing
				_autocompleteInput($select, $input, _options.notfoundCss);
				
				// Expand on focus?
				if (_options.expandOnFocus) {
					_expandOnFocus($select, $input, _options.expandSize);
				}
				
				// Selects all text on focus in input element
				_selectallOnClick($input);
			});
		},
		
		clear: function() {
			// Clear elements
			_cleanup(this);
		}
	};
	
	/**
	 * Private methods
	 */
	
	function _disableTabstop($select) {
		$select.attr('tabindex', '-1');
	}
	
	// If $().jqCombo() is called twice, clean up previous init
	function _cleanup($select) {
		$select.next('select.jqcombo-clone').remove();
		$select.next('input.jqcombo-input').remove();
		$select.off('.jqcombo');
	}
	
	// Adds a input element after the textbox and positions it
	function _inputAfter($select) {
		var $input = $('<input/>');
		
		// Default options for input
		$input.css({
			position: 'absolute'
		});
		
		// Save class so we can remove later if needed
		$input.addClass('jqcombo-input');
				
		// Set text value to select value
		$input.val($select.find('option:selected').text());
		
		// Insert into DOM
		$select.after($input);
		
		// Return to callee with the new input element
		return $input;
	}
	
	// Positions the input element
	function _positionInput($select, $input) {
		var offset = _positionCorrection();
		$(window).resize(function() {
			$input.css({
				top		: $select.position().top + offset.top,
				left	: $select.position().left + offset.left,
				width	: $select.width() + offset.width,
				height	: $select.height() + offset.height
			});
		}).resize();
	}
	
	// Get position correction for input based on browsers
	// Based on Windows 7 / Mac OS X Lion and latest browser versions
	function _positionCorrection() {
		// jQuery.browser is deprecated, so we may want to rewrite this
		// functionality ourselves, as we can't rely on feature detection here
		var defaultOffset = {
			top		: 0,
			left	: 0,
			width	: -22,
			height	: -4
		};
		var offset = {};
		var browser = _browser();
		var os = _os();
		if (browser.chrome) {
			offset = {
				top		: 2,
				left	: 2,
				width	: -20
			}
		}
		if (browser.safari || (browser.chrome && os.mac)) { // also for chrome on mac
			offset = {
				top		: 2,
				left	: 2,
				width	: 4,
				height	: -2		
			}
			if (os.mac) {
				offset.width = -25;
				offset.height = -6;
			}
		}
		if (browser.msie) {
		}		
		if (browser.mozilla) {
			if (os.mac) {
				offset = {
					height: -6,
					width: 0
				}
			}
		}
		if (browser.opera) {
			offset = {
				top		: 0,
				left	: 0,
				width	: -16,
				height	: 0				
			}
			if (os.mac) {
				offset = {
					top		: 1,
					left	: 2,
					width	: -22,
					height	: -3
				}
			}
		}
		return $.extend(defaultOffset, offset);
	}
	
	// Watch the select box for changes and updated input
	function _watchChanges($select, $input) {
		$select.on('change.jqcombo', function() {
			$input.val($select.find('option:selected').text());
		});
	}
	
	// The beating heart: autocomplete function
	function _autocompleteInput($select, $input, notfoundCss) {
		var lastKeycode = null;
		var origInputCss = _origCss($input, notfoundCss);
		
		$input.on('keyup.jqcombo', function(e) {
			// Sore last pressed key for lookback
			if ($.inArray(e.keyCode, keys.lookback) == -1) {
				lastKeycode = e.keyCode;
			}
			
			// Select all on tab
			if (e.keyCode === 9 ||
				($.inArray(e.keyCode, keys.lookback) >= 0 && lastKeycode === 9)) {
				$input.select();
				return;
			}
			
			// Arrow navigation
			if ($.inArray(e.keyCode, keys.down) >= 0) {
				var o = $select.find('option:selected').next();	
				$select.val(o.val());
				$input.val(o.text()).select();
				return;
			} else if ($.inArray(e.keyCode, keys.up) >= 0) {
				var o = $select.find('option:selected').prev();	
				$select.val(o.val());
				$input.val(o.text()).select();
				return;
			}
			
			// Wait for all keys to be released
			if (keypressCounter > 0) {
				return;
			}
			
			// Ignore the current or lookback key?
			if ($.inArray(e.keyCode, keys.ignore) >= 0 ||
				($.inArray(e.keyCode, keys.lookback) >= 0 && $.inArray(lastKeycode, keys.ignore) >= 0)) {
				return;
			}
			
			// Resets the notfound color
			$input.css(origInputCss);
			
			// Gets the current input text
			var typedText = $input.val();
			
			// Find an option containing our text (case-insensitive)
			var $match = $select.find('option:startswithi("' + typedText + '"):eq(0)');
			var matchedText = $match.text();
			
			// Do we have a match?
			if ($match.length) {
				// Set select box to match
				$select.val($match.val());
				
				// Make selection if not in noselectionKeys list
				if ($.inArray(e.keyCode, keys.noselection) == -1) {
					$input.val(matchedText);
					_createSelection($input, typedText.length, matchedText.length)
				}				
			} else {
				// Set select box to option without value
				// TODO: if no such option exist?
				$select.val('');
				
				// Set notfound color on input
				$input.css(notfoundCss);
			}			
		});
	}
	
	// Expand selectbox on input focus, collapse on input blur
	function _expandOnFocus($select, $input, size) {
		// Set styles for $select at focus, update on window resize
		var focusCss;
		$(window).resize(function() {
			focusCss = {
				position: 'absolute',
				left	: $select.position().left,
				top		: $select.position().top + $select.height() + 5,
				zIndex	: 1
			};
		}).resize();		
		
		// Save original settings to restore with blur
		var origSize = $select.attr('size') || 0;
		var origCss = _origCss($select, focusCss);
		
		// Create clone of select element to retain flow on absolute positioning
		var $clone = $select.clone();
		
		// Remove name/id attributes on clone to prevent issues with form submission, labels, etc
		$clone.removeAttr('id name');
		
		// Hide clone from flow for now
		$clone.css({ visibility: 'hidden' }).hide();
		
		// Add clone class so we can target it in _cleanup()
		$clone.removeClass('jqcombo').addClass('jqcombo-clone');
		
		// Add to DOM
		$select.after($clone);
		
		// Expand
		$input.on('focus.jqcombo', function() {
			// Asynchronous to allow collapse invoked by $select.blur to run first
			setTimeout(function() {
				$select.attr('size', size);
				$select.css(focusCss);
				$input.css('z-index', 2);
				$clone.css({ display: 'inline-block' });
			}, 0);
		});
		
		// Cancel collapse invoked by $input.blur
		$select.on('focus.jqcombo', function() {
			if (blurTimer) {
				clearTimeout(blurTimer);
			}
		});
		
		// Timer to allow focus event on select to cancel collapse
		var blurTimer;
		
		// Collapse
		var collapse = function(e) {
			// Asynchronous to allow focus event $select to cancel collapse
			blurTimer = setTimeout(function() {
				$select.attr('size', origSize);
				$select.css(origCss);
				$input.css('z-index', 'auto');
				$clone.hide();
				blurTimer = null;
			}, 0);
		}
		
		// Focus away from $input
		$input.on('blur.jqcombo', collapse);
		
		// Focus away from (expanded) $select
		$select.on('blur.jqcombo click.jqcombo', collapse);
	}
	
	// Keypress counter and repeater neutralizer
	// Used in autocompleter to only continue if 
	// user finished pressing any keys
	function _initKeypressCounter($input) {
		var keysPressed = [];
		$input.on('keydown.jqcombo', function(e) {
			// Store currently pressed keys
			if ($.inArray(e.keyCode, keysPressed) == -1) {
				keysPressed.push(e.keyCode);
			}
			keypressCounter = keysPressed.length;
		});
		
		$input.on('keyup.jqcombo', function(e) {			
			// Remove currently pressed key from store
			var index = $.inArray(e.keyCode, keysPressed);
			if (index >= 0) {
				keysPressed.splice(index, 1);
			}
			keypressCounter = keysPressed.length;
		});	
	}
		
	function _origCss($element, cssKeys) {
		var ret = {};
		for (var i in cssKeys) {
			ret[i] = $element.css(i);
		}
		return ret;
	}
	
	// @source http://stackoverflow.com/a/646662/938297
	function _createSelection($field, start, end) {
		var field = $field[0];
		if (field.createTextRange) {
			var selRange = field.createTextRange();
			selRange.collapse(true);
			selRange.moveStart('character', start);
			selRange.moveEnd('character', end);
			selRange.select();
		} else if (field.setSelectionRange) {
			field.setSelectionRange(start, end);
		} else if (field.selectionStart) {
			field.selectionStart = start;
			field.selectionEnd = end;
		}
		field.focus();
	}       
	
	// Selects all input gets focus by click only
	function _selectallOnClick($input) {
		$input.on('click.jqcombo', function(e) {
			$(this).select();
		});
	}
	
	// @source http://www.abeautifulsite.net/blog/2011/11/detecting-mobile-devices-with-javascript/
	function _isMobile() {
		return {
			android: function() {
				return navigator.userAgent.match(/Android/i) ? true : false;
			}(),
			blackberry: function() {
				return navigator.userAgent.match(/BlackBerry/i) ? true : false;
			}(),
			ios: function() {
				return navigator.userAgent.match(/iPhone|iPad|iPod/i) ? true : false;
			}(),
			windows: function() {
				return navigator.userAgent.match(/IEMobile/i) ? true : false;
			}(),
			any: function() {
				return navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|IEMobile/i) ? true : false;
			}()
		}
	}
	
	function _os() {
		return {
			mac: function() {
				return navigator.userAgent.match(/Mac/i) ? true : false;
			}(),
			windows: function() {
				return navigator.userAgent.match(/Win/i) ? true : false;
			}()
		}		
	}
	
	// @source https://github.com/louisremi/jquery.browser/blob/master/jquery.browser.js
	function _browser() {
		var ua = navigator.userAgent.toLowerCase(),
			match,
			i = 0,
			
			// Useragent RegExp
			rbrowsers = [
				/(chrome)[\/]([\w.]+)/,
				/(safari)[\/]([\w.]+)/,
				/(opera)(?:.*version)?[ \/]([\w.]+)/,
				/(msie) ([\w.]+)/,
				/(mozilla)(?:.*? rv:([\w.]+))?/
			];
			
		var browser = {};
		do {
			if ( (match = rbrowsers[i].exec( ua )) && match[1] ) {
				browser[ match[1] ] = true;
				browser.version = match[2] || "0";
				break;
			}
		} while (i++ < rbrowsers.length)
		
		return browser;
	}
	
	// Register the plugin on the jQuery namespace
	$.fn.jqCombo = function(method) {
		if ( methods[method] ) {
			// Plugin method explicitly called
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if ( typeof method === 'object' || ! method ) {
			// Default method: init
			return methods.init.apply(this, arguments);
		} else {
			// Method not found
			$.error('Method ' +  method + ' does not exist on jQuery.jqCombo');
			return false;
		}
  
	};
	
})(jQuery);

// @source http://stackoverflow.com/a/4936066/938297
$.extend($.expr[':'], {
	'startswithi': function(elem, i, match, array) {
		return (elem.textContent || elem.innerText || '').toLowerCase()
			.indexOf((match[3] || "").toLowerCase()) == 0;
	}
});