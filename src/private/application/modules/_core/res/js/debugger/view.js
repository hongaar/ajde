;
$(document).ready(function() {
	var height = 400;
	var max = $(window).height() - 10;
	
	AC.Shortcut.add('Ctrl+1', function() { toggleSection('#ajdeDebuggerDump div'); });
	AC.Shortcut.add('Ctrl+2', function() { toggleSection('#ajdeDebuggerRequest'); });
	AC.Shortcut.add('Ctrl+3', function() {  });
	AC.Shortcut.add('Ctrl+4', function() {  });
	AC.Shortcut.add('Ctrl+5', function() { toggleSection('#ajdeDebuggerTimers'); });
	AC.Shortcut.add('Ctrl+6', function() { toggleSection('#ajdeDebuggerSession'); });
	AC.Shortcut.add('Ctrl+7', function() { toggleSection('#ajdeDebuggerACL'); });
	AC.Shortcut.add('Ctrl+8', function() { toggleSection('#ajdeDebuggerQueries'); });
	AC.Shortcut.add('Esc', toggleDebugger);
	
	$('#ajdeDebuggerHeader').click(toggleDebugger);
	$('#ajdeDebugger').dblclick(hideDebugger);
		
	// Ajax var_dump catcher
	$(document).ajaxComplete(function(e, xhr, settings) {
		if (xhr.responseText.indexOf('UNCAUGHT EXCEPTION') > -1 || xhr.responseText.indexOf('Exception thrown') > -1) {
			$('#ajdeDebuggerDump').prev().html('Response contains <code>uncaught exception</code>');
			$('#ajdeDebuggerDump').html(xhr.responseText).slideDown('slow');
			showDebugger(true);
		} else if (xhr.responseText.indexOf('xdebug-var-dump') > -1) {
			$('#ajdeDebuggerDump').prev().html('Response contains <code>var_dump</code>');
			$('#ajdeDebuggerDump').html(xhr.responseText).slideDown('slow');
			showDebugger(true);
		}
	});
	
	function toggleDebugger() {
		if (parseInt($('#ajdeDebuggerContent').css('height')) < height) {
			showDebugger();
		} else if (parseInt($('#ajdeDebuggerContent').css('height')) > height) {			
			hideDebugger();
		} else {
			showDebugger(true);
		}
	}
	
	function toggleSection(ident) {
		showDebugger();
		$(ident).slideToggle('fast');
	}
	
	function showDebugger(fullscreen) {
//		if (parseInt($('#ajdeDebuggerContent').css('height')) < height) {
			$('#ajdeDebuggerContent').animate({height: (fullscreen ? max : height) + 'px'}, 'fast', function() {
				$('#ajdeDebuggerContent').css({overflowY: 'scroll'});
			});
//		}
	};
	
	function hideDebugger() {
//		if (parseInt($('#ajdeDebuggerContent').css('height')) == height) {
			$('#ajdeDebuggerContent').animate({height: '0'}, 'fast');
//		}
	};
});