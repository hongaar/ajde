;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Timer === "undefined") {App.Timer = function(){}};

App.Timer.Widget = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var timer = {};
	var elapsedSeconds = 0;
	
	var span = {
		week: 144000,
		day: 28800,
		hour: 3600,
		minute: 60
	};
	
	var formatTimespan = function(seconds) {	
		if (seconds == 0) {
			return '0s';
		}
	
		var weeks = Math.floor(seconds / span.week);
		seconds = seconds - (weeks * span.week);

		var days = Math.floor(seconds / span.day);
		seconds = seconds - (days * span.day);

		var hours = Math.floor(seconds / span.hour);
		seconds = seconds - (hours * span.hour);

		var minutes = Math.floor(seconds / span.minute);
		seconds = seconds - (minutes * span.minute);

		var output = '';
		if (weeks) {
			output += weeks + 'w ';
		}
		if (days) {
			output += days + 'd ';
		}
		if (hours) {
			output += hours + 'h ';
		}
		if (minutes) {
			output += minutes + 'm ';
		}
		if (seconds) {
			output += seconds + 's ';
		}
		return output.trim();
	};
	
	var initActive = function(issue) {
		$('.timerwidget_container').removeClass('paused').addClass('active open');
		$('.timerwidget .issue').text(issue);
		elapsedSeconds = 0;
		startTimer();
	};

	var doPause = function() {
		$('.timerwidget_container').removeClass('active').addClass('paused');
		cancelTimer();
	};

	var doResume = function() {
		$('.timerwidget_container').removeClass('paused').addClass('active');
		startTimer();
	};

	var doDone = function() {
		$('.timerwidget_container').removeClass('paused').removeClass('active');
		elapsedSeconds = 0;
		cancelTimer();
	};

	var startTimer = function() {
		cancelTimer();
		timer = accurateInterval(1000, function() {
			elapsedSeconds++;
			$('.timerwidget .duration').text(formatTimespan(elapsedSeconds));
		});
		$('.timerwidget .duration').text(formatTimespan(elapsedSeconds));
	};

	var cancelTimer = function() {
		if (timer.cancel) {
			timer.cancel();
		}
	};
	
	return {

		init: function() {
			elapsedSeconds = $('.timerwidget .duration').data('seconds');
			if ($('.timerwidget_container').hasClass('active')) {				
				startTimer();
			} else if ($('.timerwidget_container').hasClass('paused')) {
				$('.timerwidget .duration').text(formatTimespan(elapsedSeconds));
			}
			$('.timerwidget a.pause').live('click', this.pause);
			$('.timerwidget a.resume').live('click', this.resume);
			$('.timerwidget a.done').live('click', this.done);
		},
			
		pause: function(e) {
			e.stopPropagation();
			var url = 'timer/pause.json';
			$.post(url, {}, function(data) {										
				if (data.success === true) {
					infoHandler('Timer paused');
					doPause();
				} else {
					warningHandler(data.message);
				}
			}, 'json').error(function(jqXHR, message, exception) {
				if (exception == 'Unauthorized' || exception == 'Forbidden') {
					warningHandler(i18n.forbiddenWarning);
				} else {
					errorHandler(i18n.requestError + ' (' + exception + ')');
				}
			});
			return false;
		},
	
		resume: function(e) {
			e.stopPropagation();
			var url = 'timer/resume.json';
			$.post(url, {}, function(data) {										
				if (data.success === true) {
					infoHandler('Timer resumed');
					doResume();
				} else {
					warningHandler(data.message);
				}
			}, 'json').error(function(jqXHR, message, exception) {
				if (exception == 'Unauthorized' || exception == 'Forbidden') {
					warningHandler(i18n.forbiddenWarning);
				} else {
					errorHandler(i18n.requestError + ' (' + exception + ')');
				}
			});
			return false;
		},
	
		done: function(e) {
			e.stopPropagation();
			var url = 'timer/done.json';
			$.post(url, {}, function(data) {										
				if (data.success === true) {
					infoHandler('Worklog saved');
					doDone();
				} else {
					warningHandler(data.message);
				}
			}, 'json').error(function(jqXHR, message, exception) {
				if (exception == 'Unauthorized' || exception == 'Forbidden') {
					warningHandler(i18n.forbiddenWarning);
				} else {
					errorHandler(i18n.requestError + ' (' + exception + ')');
				}
			});
			return false;
		},
	
		work: function(nodeId) {
			var url = 'timer/work.json';
			var data = {
				node: nodeId,
				'_token': $('input[name=_token]').val()
			};
			$.post(url, data, function(data) {										
				if (data.success === true) {
					infoHandler('Timer started');
					initActive(data.issue);
				} else {
					warningHandler(data.message);
				}
			}, 'json').error(function(jqXHR, message, exception) {
				if (exception == 'Unauthorized' || exception == 'Forbidden') {
					warningHandler(i18n.forbiddenWarning);
				} else {
					errorHandler(i18n.requestError + ' (' + exception + ')');
				}
			});
		}

	};
}();

$(document).ready(function() {
	App.Timer.Widget.init();
});

// https://gist.github.com/Squeegy/1d99b3cd81d610ac7351
(function() {
  window.accurateInterval = function(time, fn) {
    var cancel, nextAt, timeout, wrapper, _ref;
    nextAt = new Date().getTime() + time;
    timeout = null;
    if (typeof time === 'function') _ref = [time, fn], fn = _ref[0], time = _ref[1];
    wrapper = function() {
      nextAt += time;
      timeout = setTimeout(wrapper, nextAt - new Date().getTime());
      return fn();
    };
    cancel = function() {
      return clearTimeout(timeout);
    };
    timeout = setTimeout(wrapper, nextAt - new Date().getTime());
    return {
      cancel: cancel
    };
  };
}).call(this);