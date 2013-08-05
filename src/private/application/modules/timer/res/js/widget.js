;
if (typeof App === "undefined") {App = function(){}};
if (typeof App.Timer === "undefined") {App.Timer = function(){}};

App.Timer.Widget = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;
	
	var timer = {};
	var elapsedSeconds = 0;
	
	var initActive = function(issue, display) {
		$('.timerwidget_container').removeClass('paused').addClass('active open');
		$('.timerwidget .issue a').attr('href', 'issue/view?edit=' + issue);
		$('.timerwidget .issue a').text(display);
		elapsedSeconds = 0;
		startTimer();
		if (AC && AC.Crud && AC.Crud.List) {
			AC.Crud.List.updateView($('form.ACCrudList').children(':eq(0)'));
		}
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
		if (AC && AC.Crud && AC.Crud.List) {
			AC.Crud.List.updateView($('form.ACCrudList').children(':eq(0)'));
		}
		elapsedSeconds = 0;
		cancelTimer();
	};

	var doCancel = function() {
		$('.timerwidget_container').removeClass('paused').removeClass('active');
		elapsedSeconds = 0;
		cancelTimer();
	};

	var startTimer = function() {
		cancelTimer();
		timer = accurateInterval(1000, function() {
			elapsedSeconds++;
			$('.timerwidget .duration').text(AC.Crud.Edit.Timespan.formatTimespan(elapsedSeconds));
		});
		if (elapsedSeconds) {
			$('.timerwidget .duration').text(AC.Crud.Edit.Timespan.formatTimespan(elapsedSeconds));
		} else {
			$('.timerwidget .duration').text('0s');
		}
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
				$('.timerwidget .duration').text(AC.Crud.Edit.Timespan.formatTimespan(elapsedSeconds));
			}
			$('.timerwidget a.pause').live('click', this.pause);
			$('.timerwidget a.resume').live('click', this.resume);
			$('.timerwidget a.done').live('click', this.done);
			$('.timerwidget a.cancel').live('click', this.cancel);
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
			$('#worklog').modal('show');
			
			$('#work_seconds').val(elapsedSeconds);
			$('#work_span').val(AC.Crud.Edit.Timespan.formatTimespan(elapsedSeconds));
			
			$('#worklog button.save').off('click');
			$('#worklog button.save').on('click', function() {
				
				var self = this;
				$(self).text('saving...');
				
				var url = 'timer/done.json';
				var data = {
					seconds: $('#work_seconds').val(),
					description: $('#worklog textarea').val(),
					status: $('input[name=work_issue_status]:checked').val(),
					'_token': $('input[name=_token]').val()
				};
				$.post(url, data, function(data) {										
					if (data.success === true) {
						$('#worklog').modal('hide');
						$(self).text('save');
						$('#work_description').val('');
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
				
			});
			
			return false;
		},
	
		cancel: function(e) {
			e.stopPropagation();
				
			var url = 'timer/cancel.json';
			var data = {
				'_token': $('input[name=_token]').val()
			};
			$.post(url, data, function(data) {										
				if (data.success === true) {
					infoHandler('Worklog cancelled');
					doCancel();
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
					initActive(data.issue, data.display);
				} else {
					warningHandler(data.message);
					$('.timerwidget_container').addClass('open');
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