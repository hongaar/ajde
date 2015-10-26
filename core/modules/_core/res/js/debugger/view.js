;
if (typeof AC === "undefined") {
    AC = function () {
    }
}
AC.Debugger = function() {

	var height = 400;
	var max = $(window).height() - 10;

	return {

		init: function() {

			AC.Shortcut.add('Alt+1', function() { this.toggleSection('#ajdeDebuggerDump div'); });
			AC.Shortcut.add('Alt+2', function() { this.toggleSection('#ajdeDebuggerRequest'); });
			AC.Shortcut.add('Alt+3', function() {  });
			AC.Shortcut.add('Alt+4', function() {  });
			AC.Shortcut.add('Alt+5', function() { this.toggleSection('#ajdeDebuggerTimers'); });
			AC.Shortcut.add('Alt+6', function() { this.toggleSection('#ajdeDebuggerSession'); });
			AC.Shortcut.add('Alt+7', function() { this.toggleSection('#ajdeDebuggerACL'); });
			AC.Shortcut.add('Alt+8', function() { this.toggleSection('#ajdeDebuggerQueries'); });
			AC.Shortcut.add('Esc', this.toggleDebugger);

			$('#ajdeDebuggerHeader').click(this.toggleDebugger);
			$('#ajdeDebugger').dblclick(this.hideDebugger);

			// Ajax var_dump catcher
			$(document).ajaxComplete(function(e, xhr, settings) {
				if (xhr.responseText.indexOf('UNCAUGHT EXCEPTION') > -1 || xhr.responseText.indexOf('Exception thrown') > -1) {
					$('#ajdeDebuggerDump').prev().html('Response contains uncaught exception');
					$('#ajdeDebuggerDump').html(xhr.responseText).slideDown('slow');
					AC.Debugger.showDebugger(true);
				} else if (xhr.responseText.indexOf('xdebug-var-dump') > -1) {
					$('#ajdeDebuggerDump').prev().html('Response contains variable dump');
					$('#ajdeDebuggerDump').html(xhr.responseText).slideDown('slow');
					AC.Debugger.showDebugger(true);
				}
			});

		},

		toggleDebugger: function() {
			if (parseInt($('#ajdeDebuggerContent').css('height')) < height) {
				AC.Debugger.showDebugger();
			} else if (parseInt($('#ajdeDebuggerContent').css('height')) > height) {
				AC.Debugger.hideDebugger();
			} else {
				AC.Debugger.showDebugger(true);
			}
		},

		toggleSection: function(ident) {
			AC.Debugger.showDebugger();
			$(ident).slideToggle('fast');
		},

		showDebugger: function(fullscreen) {
			$('#ajdeDebuggerContent').animate({height: (fullscreen ? max : height) + 'px'}, 'fast', function() {
				$('#ajdeDebuggerContent').css({overflowY: 'scroll'});
			});
		},

		hideDebugger: function() {
			$('#ajdeDebuggerContent').animate({height: '0'}, 'fast');
		}

	};
}();

$(document).ready(function() {
	AC.Debugger.init();
});
