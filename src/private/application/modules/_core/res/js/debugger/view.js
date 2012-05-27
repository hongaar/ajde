;
$(document).ready(function() {
	var height = 400;
	
	AC.Shortcut.add('Ctrl+1', function() { toggleSection('#ajdeDebuggerDump div'); });
	AC.Shortcut.add('Ctrl+2', function() { toggleSection('#ajdeDebuggerRequest'); });
	AC.Shortcut.add('Ctrl+3', function() {  });
	AC.Shortcut.add('Ctrl+4', function() {  });
	AC.Shortcut.add('Ctrl+5', function() { toggleSection('#ajdeDebuggerTimers'); });
	AC.Shortcut.add('Ctrl+6', function() { toggleSection('#ajdeDebuggerSession'); });
	AC.Shortcut.add('Ctrl+7', function() { toggleSection('#ajdeDebuggerACL'); });
	AC.Shortcut.add('Ctrl+8', function() { toggleSection('#ajdeDebuggerQueries'); });
	AC.Shortcut.add('Esc', toggleDebugger);
	
	$('#ajdeDebuggerHeader').click(showDebugger);
	$('#ajdeDebuggerHeader').click(hideDebugger);
	$('#ajdeDebugger').dblclick(hideDebugger);
	
	function toggleDebugger() {
		if (parseInt($('#ajdeDebuggerContent').css('height')) < height) {
			showDebugger();
		} else {
			hideDebugger();
		}
	}
	
	function toggleSection(ident) {
		showDebugger();
		$(ident).slideToggle('fast');
	}
	
	function showDebugger() {
		if (parseInt($('#ajdeDebuggerContent').css('height')) < height) {
			$('#ajdeDebuggerContent').animate({height: height + 'px'}, 'fast', function() {
				$('#ajdeDebuggerContent').css({overflowY: 'scroll'});
			});
		}
	};
	
	function hideDebugger() {
		if (parseInt($('#ajdeDebuggerContent').css('height')) == height) {
			$('#ajdeDebuggerContent').animate({height: '0'}, 'fast');
		}
	};
});