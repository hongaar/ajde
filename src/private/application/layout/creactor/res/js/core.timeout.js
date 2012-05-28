;
if (typeof AC ==="undefined") {AC = function(){}};
if (typeof AC.Core ==="undefined") {AC.Core = function(){}};

AC.Core.Timeout = function() {
	
	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var gracePeriod = 300; // in seconds
	
	var expireTime;
	
	var warningTimer;
	var updateTimer;
	var reloadTimer;
	
	var showWarning = function() {
		alert(i18n.timeoutWarning);
		updateTimer = setInterval(updateWarning, 1000);
		updateWarning();
	};
	
	var updateWarning = function() {
		var sLeft = expireTime - new Date();
		if (sLeft < 0) {			
		} else {
			infoHandler(i18n.timeoutCountdown.replace('%s', Math.ceil(sLeft / 1000)), keepAlive);
		}
	};
	
	var keepAlive = function() {
		clearInterval(updateTimer);
		AC.Core.Timeout.clearReloadTimeout();
		$.post('user/keepalive.json', {}, function(data) {
			AC.Core.Timeout.init();
		}, 'json').error(
			function(jqXHR, message, exception) {
				errorHandler(i18n.requestError + ' (' + exception + ')');
			}
		);
	};
	
	var timeout = function() {
		clearInterval(updateTimer);
		warningHandler(i18n.timedout);
		alert(i18n.timedout);
		setTimeout(function() {
			//window.document.location.reload(true);
		}, 10000);		
	};
	
	return {
		
		init: function() {
			// Form on page?
			if (!$('form[method="post"]').length) {
				return;
			}
			var lifetime = $('html').attr('data-lifetime'); // minutes
			var seconds = lifetime * 60;
			var curDate = new Date();
			expireTime = new Date(curDate.getTime() + (lifetime * 60000));
			AC.Core.Timeout.setWarningTimeout(seconds - gracePeriod);
			AC.Core.Timeout.setReloadTimeout(seconds);
		},
		
		setWarningTimeout: function(m) {
			var seconds = m * 1000;
			warningTimer = setTimeout(showWarning, seconds);
		},
		
		setReloadTimeout: function(m) {
			var seconds = m * 1000;
			reloadTimer = setTimeout(timeout, seconds);
		},
		
		clearWarningTimeout: function() {
			clearTimeout(warningTimer);
		},
		
		clearReloadTimeout: function() {
			clearTimeout(reloadTimer);
		}		
		
	};
}();

$(document).ready(function() {
	AC.Core.Timeout.init();
});